import { Injectable } from '@angular/core';

import { ApiService, LocalStorageService } from "../core";

import { User } from './user';

@Injectable()
export class UserService {

  public user: User;

  constructor(
    private api: ApiService,
    private localStorage: LocalStorageService
  ) {
    // Reauthenticate callback handler
    let renewTokenHandler = (request, error): Promise<any> => {
      if(this.localStorage.user) {
        return this.authenticate().then(() => { this.api.request(request) });
      } else {
        return Promise.reject(error);
      }
    };

    // Embed token_expired and token_not_provided error handler to auto renew token
    this.api.embedErrorHandler("token_expired", renewTokenHandler);
    this.api.embedErrorHandler("token_not_provided", renewTokenHandler);
  }

  /**
   * Create new user
   * @param user
   * @param password
   * @returns {Promise<Response>}
   */
  public createUser(user: User, password: string): Promise<boolean> {
    return this.api.post('/users', { user, password })
      .then(response => { return response.status == 201; })
  }

  /**
   * Request new token
   * @param email
   * @param password
   * @returns {Promise<Response>}
   */
  public authenticate(email?: string, password?: string): Promise<User> {
    if(!email && !password) {
      let user = this.localStorage.user;
      if(user && user.email && user.password) {
        // If email and password not given
        // Then authenticate from localStorage if information exist
        email = user.email;
        password = user.password;
      } else {
        // If user information not exist in localStorage
        // Then reject the authenticate process (Promise.reject)
        this.localStorage.token = null;
        this.localStorage.user = null;
        return Promise.reject({error: 'No user in storage'});
      }
    }

    return this.api.post('/authentication', { email, password, user: true })
      .then(response => {
        this.localStorage.token = response.json().token;
        this.localStorage.user = { email, password };

        return this.user = response.json().user;
      });
  }

  /**
   * Resolve user if authenticate (Check expired)
   */
  public getAuthenticatedUser(): Promise<User> {
    if(!!this.localStorage.token || !!this.localStorage.user) {
      return this.api.get('/authentication')
        .then(response => this.user = response.json().user as User)
    }
  }

  /**
   * Logout (clear authentication token)
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
