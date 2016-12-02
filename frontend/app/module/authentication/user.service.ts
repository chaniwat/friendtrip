import { Injectable } from '@angular/core';

import { ApiService, LocalStorageService } from "../core";

import { User } from './user';
import { ErrorResponse } from "../core/api.service";

@Injectable()
export class UserService {

  public user: User;

  constructor(
    private api: ApiService,
    private localStorage: LocalStorageService
  ) {

    // Reauthenticate handler
    let reauthenticateHandler = (request): Promise<any> => {
      let user = this.localStorage.user;
      if(user && user.email && user.password) {
        // If email and password not given
        // Then authenticate from localStorage if information exist
        return this.authenticate(user.email, user.password).then(() => this.api.request(request));
      } else {
        // If user information not exist in localStorage
        // Then reject the authenticate process (Promise.reject)
        this.localStorage.token = null;
        this.localStorage.user = null;
        return Promise.reject(new ErrorResponse("cannot_re_authenticate"));
      }
    }

    // Embed token_expired and token_not_provided error handler to auto renew token
    this.api.addErrorHandler("token_expired", reauthenticateHandler);
    this.api.addErrorHandler("token_not_provided", reauthenticateHandler);

  }

  /**
   * Create new user
   * @param user username
   * @param password password
   * @returns {Promise<Response>}
   */
  public createUser(user: User, password: string): Promise<boolean> {
    return this.api.post('users', { user, password })
      .then(() => true)
  }

  /**
   * Request new token (Authenticate)
   * @param email
   * @param password
   * @returns {Promise<Response>}
   */
  public authenticate(email: string, password: string): Promise<User> {
    return this.api.post('authentication', { email, password, user: true })
      .then(response => {
        this.localStorage.token = response.token;
        this.localStorage.user = { email, password };

        return this.user = response.user;
      });
  }

  /**
   * Get authenticate user
   */
  public getAuthenticatedUser(): Promise<User> {
    if(!!this.localStorage.token || !!this.localStorage.user) {
      return this.api.get('authentication')
        .then(response => this.user = response.json().user as User);
    } else {
      return Promise.reject(new ErrorResponse("no_token_or_user"));
    }
  }

  /**
   * Logout (Clear token and user)
   */
  public logout() {
    this.localStorage.token = null;
    this.localStorage.user = null;
    this.user = null;
  }

  /**
   * Is having user
   */
  public isHavingUser(): boolean {
    return !!this.user;
  }

}
