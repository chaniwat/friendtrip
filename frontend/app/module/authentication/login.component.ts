import { Component } from '@angular/core';
import { Router } from "@angular/router";
import { NgForm } from '@angular/forms';

import { UserService } from "./user.service";

@Component({
  selector: 'page-login',
  templateUrl: 'login.component.html',
  styleUrls: ['login.component.scss']
})
export class LoginComponent {

  private submitted: boolean = false;

  constructor(
    private router: Router,
    private userService: UserService
  ) {}

  onSubmit(f: NgForm) {
    if(this.submitted) {
      return;
    }
    this.submitted = true;

    this.userService.authenticate(f.value.email, f.value.password)
    .then(() => {
      toastr.success('Login success');
      this.router.navigateByUrl('/');
    })
    .catch(error => {
      toastr.error('Error: ' + error);
      this.submitted = false;
    });
  }

}
