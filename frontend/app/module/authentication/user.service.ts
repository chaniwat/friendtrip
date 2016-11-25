import { Injectable } from '@angular/core';
import { RequestMethod } from '@angular/http';

import { ApiService, LocalStorageService, Request } from "../core";

import { User } from './user';

@Injectable()
export class UserService {

  user: User;

  constructor(
    private api: ApiService,
    private localStorage: LocalStorageService
  ) {

    // Reauthenticate callback
    let reAuthHandler = (request, error): Promise<any> => {
      if(this.localStorage.user) {
        return this.authenticate()
        .then(() => {
          switch(request.method) {
            case RequestMethod.Get: return this.api.get(request);
            case RequestMethod.Post: return this.api.post(request);
          }
        });
      } else {
        return Promise.reject(error);
      }
    };

    // Inject token_expired and token_not_provided error to auto renew token
    this.api.injectErrorHandler("token_expired", reAuthHandler);
    this.api.injectErrorHandler("token_not_provided", reAuthHandler);

  }

  /**
  * Create new user
  * @param user
  * @param password
  * @returns {Promise<Response>}
  */
  public createUser(user: User, password: string): Promise<boolean> {
    let body = JSON.stringify({ user, password });

    return this.api.post(this.api.makeRequest('/users', body))
    .then(response => { return response.status == 201; })
    .catch(this.handleError.bind(this));
  }

  /**
  * Request new token
  * @param email
  * @param password
  * @returns {Promise<Response>}
  */
  public authenticate(email?: string, password?: string): Promise<User> {
    if(!email && !password) {
      if(this.localStorage.user) {
        // If email and password not given
        // Then authenticate from localStorage if information exist
        let user = this.localStorage.user;
        email = user.email;
        password = user.password;
      } else {
        // If user information not exist in localStorage
        // Then reject the authenticate process (Promise.reject)
        return Promise.reject('No user in storage');
      }
    }

    let body = JSON.stringify({ email, password, user: true });

    return this.api.post(this.api.makeRequest('/authentication', body))
    .then(response => {
      let params = response.json();
      this.localStorage.token = params.token;
      this.localStorage.user = { email, password };
      return this.user = params.user;
    })
    .catch(this.handleError.bind(this));
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
  * Is user logged in
  */
  public isLoggedIn(): boolean {
    return !!this.user;
  }

  /**
  * Resolve user if authenticate (Check expired)
  */
  public resolveUser(): Promise<User> {
    if(!!this.localStorage.token || !!this.localStorage.user) {
        return this.getAuthenticationUser();
    }
  }

  /**
  * Get authentication user
  * @param token
  * @returns {Promise<Response>}
  */
  public getAuthenticationUser(): Promise<User> {
    return this.api.get(this.api.makeRequest('/authentication'))
    .then(response => this.user = response.json().user as User)
    .catch(this.handleError.bind(this));
  }

  /**
  * Handle any promises error
  */
  private handleError(error: any): Promise<any> {
    error = error.json().error;
    return Promise.reject(error);
  }
}
