import { Component } from '@angular/core';
import { Router } from "@angular/router";
import { NgForm } from '@angular/forms';

import { User } from "./user";
import { UserService } from "./user.service";
import { ErrorResponse } from "../core/api.service";

@Component({
  selector: 'page-register',
  templateUrl: 'register.component.html'
})
export class RegisterComponent {

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

    let user: User = _.omit(f.value, ['password']) as User;
    let password: string = f.value.password;

    this.userService.createUser(user, password)
    .then(result => {
      if(result) {
        toastr.success('Register successful');
        this.router.navigateByUrl("/");
      }
    })
    .catch((error: ErrorResponse) => {
      toastr.error('Something wrong: ' + error.message);
      this.submitted = false;
    });
  }

}
