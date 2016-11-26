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
    return this.getObject('auth_token');
  }

  /**
  * Set authentication token
  */
  public set token(token: string) {
    this.setObject('auth_token', token);
  }

  /**
   * Get user object
   */
  public get user(): { email: string, password: string } {
    return this.getObject('user');
  }

  /**
   * Set user object
   */
  public set user(user: { email: string, password: string }) {
    this.setObject('user', user);
  }

  /**
   * Get any object
   */
  public getObject(key): any {
    return JSON.parse(this.localStorage.getItem(key));
  }

  /**
   * Set any object
   */
  public setObject(key, value) {
    if(value == null) {
      this.localStorage.removeItem(key);
    } else {
      this.localStorage.setItem(key, JSON.stringify(value));
    }
  }

}
