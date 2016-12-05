import { Component } from '@angular/core';
import { Router } from '@angular/router';

import { UserService, User } from "../module/authentication";
import { FormGroup, FormBuilder, Validators, FormControl } from "@angular/forms";
import { ErrorResponse } from "../module/core/api.service";

@Component({
  selector: 'comp-navbar',
  templateUrl: 'navbar.component.html',
  styleUrls: ['navbar.component.scss']
})
export class NavbarComponent {

  constructor(
    private userService: UserService,
    private router: Router
  ) {}

  get user(): User {
    return this.userService.user;
  }

  get isLoggedIn(): boolean {
    return this.userService.isHavingUser();
  }

  showLoginModal() {
    this.userService.showLoginModal();
  }

  logout() {
    this.userService.logout();
    toastr.info('ออกจากระบบ');
    this.router.navigateByUrl("/");
  }

}
