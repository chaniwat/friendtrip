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
    if(this.userService.isLoggedIn()) {
      return true;
    } else {
      this.router.navigateByUrl('/login');
      toastr.warning("เข้าสู่ระบบ");
      return false;
    }
  }

}
