import { Injectable } from '@angular/core';
import { CoolLocalStorage } from 'angular2-cool-storage';

@Injectable()
export class LocalStorageService {

  constructor(
    private localStorage: CoolLocalStorage
  ) { }

  /**
  * Get authentication token
  */
  public get token(): string {
    return this.localStorage.getItem('auth_token');
  }

  /**
  * Set authentication token
  */
  public set token(token: string) {
    if(token == null) {
      localStorage.removeItem('auth_token');
    } else {
      this.localStorage.setItem('auth_token', token);
    }
  }

  public get user(): { email: string, password: string } {
    return JSON.parse(this.localStorage.getItem('user'));
  }

  public set user(user: { email: string, password: string }) {
    if(user == null) {
      localStorage.removeItem('user');
    } else {
      this.localStorage.setItem('user', JSON.stringify(user));
    }
  }

}
