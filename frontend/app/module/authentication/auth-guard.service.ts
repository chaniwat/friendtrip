import { Injectable } from '@angular/core';
import { CanActivate, CanActivateChild, Router } from '@angular/router';

import { UserService } from './user.service';

@Injectable()
export class AuthGuardService implements CanActivate, CanActivateChild {

  constructor(
    private userService: UserService,
    private router: Router
  ) { }

  canActivate(): boolean {
    return this.checkLogin();
  }

  canActivateChild(): boolean {
    return this.checkLogin();
  }

  checkLogin(): boolean {
    if(this.userService.isHavingUser()) {
      return true;
    } else {
      this.userService.showLoginModal();
      toastr.warning("เข้าสู่ระบบ");
      this.router.navigateByUrl('/');
      return false;
    }
  }

}
