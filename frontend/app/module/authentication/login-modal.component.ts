import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators, FormControl } from "@angular/forms";
import { ErrorResponse } from "../core/api.service";
import { UserService } from "./user.service";
import { Router } from "@angular/router";

@Component({
  selector: 'login-modal',
  templateUrl: 'login-modal.component.html'
})
export class LoginModalComponent implements OnInit {

  loginForm: FormGroup;
  errorMessage: string = '';
  loginSubmitted: boolean = false;

  constructor(
    private userService: UserService,
    private router: Router,
    private formBuilder: FormBuilder
  ) {
    this.loginForm = this.formBuilder.group({
      email: ['', Validators.required],
      password: ['', Validators.required]
    });
  }

  ngOnInit() { }

  get formRequiredError() {
    if(!this.loginForm) return;

    let controls = <any>this.loginForm.controls;
    return {
      email: controls.email.touched && controls.email.errors && controls.email.errors.required,
      password: controls.password.touched && controls.password.errors && controls.password.errors.required
    }
  }

  onLoginSubmit() {
    // Check empty field
    if(!this.loginForm.valid) {
      _.forEach(this.loginForm.controls, (value: FormControl) => {
        value.markAsTouched();
      });

      this.errorMessage = 'กรุณาใส่ข้อมูลให้ครบ';
      toastr.error(this.errorMessage);
      return;
    }

    if(this.loginSubmitted) return;
    this.loginSubmitted = true;
    this.errorMessage = '';

    // disable field
    let controls: any = this.loginForm.controls;
    (<FormControl>controls.email).disable();
    (<FormControl>controls.password).disable();

    this.userService.authenticate(this.loginForm.value.email, this.loginForm.value.password)
      .then(() => {
        toastr.success('Login success');
        this.userService.hideLoginModal();
        this.router.navigateByUrl('/');
      })
      .catch((error: ErrorResponse) => {
        if(error.message == 'invalid_credentials') {
          this.errorMessage = 'อีเมลล์หรือรหัสผ่านผิด';
        } else {
          this.errorMessage = 'มีข้อผิดพลาด: ' + error;
        }
        toastr.error(this.errorMessage);

        // enable field
        (<FormControl>controls.email).enable();
        (<FormControl>controls.password).enable();

        this.loginSubmitted = false;
      });
  }

}