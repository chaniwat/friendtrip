import { Component } from '@angular/core';
import { Router } from '@angular/router';

import { UserService, User } from "../module/authentication";

@Component({
  selector: 'comp-navbar',
  templateUrl: 'navbar.component.html'
})
export class NavbarComponent {

  constructor(
    private userService: UserService,
    private router: Router
  ) {}

  get user(): User {
    return this.userService.user;
  }

  isLoggedIn(): boolean {
    return this.userService.isHavingUser();
  }

  logout() {
    this.userService.logout();
    toastr.success('Logout success');
    this.router.navigateByUrl("/login");
  }

}
