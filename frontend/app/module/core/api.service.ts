import { Injectable } from '@angular/core';
import { Headers, Http, RequestMethod, RequestOptions, Response } from '@angular/http';
import { environment } from '../../../environments/environment';

import { LocalStorageService } from './localstorage.service';
import { Request } from './request';

import 'rxjs/add/operator/toPromise';

@Injectable()
export class ApiService {

  private readonly apiUrl: string = environment.protocol + '://' + environment.domain + '/api';
  private errorHandlerInjectors: {error: string, handler: Function}[] = [];
  private isLoad: boolean;

  constructor(
    private http: Http,
    private localStorage: LocalStorageService
  ) { }

  public injectErrorHandler(error: string, handler: Function) {
    this.errorHandlerInjectors.push({ error, handler });
  }

  public isLoading(): boolean {
    return this.isLoad;
  }

  public generateApiUrl(path: string, params?: any): string {
    let urlParam: string;
    if(params != null) {
      urlParam = '?';
      _.forEach(params, (value: any, key: any) => {
        urlParam += `${key}=${value}`;
      });
    }

    return this.apiUrl + path + (params ? urlParam : '');
  }

  public makeRequest(url: string, paramsOrBody?: any|string, header?: any): Request {
    if(paramsOrBody && typeof paramsOrBody == "string") {
      return new Request(url, undefined, paramsOrBody, undefined, header);
    } else {
      return new Request(url, paramsOrBody, undefined, undefined, header);
    }
  }

  private makeHeader(params?: any): Headers {
    let headers = new Headers({ 'Accept': 'application/json', 'Content-Type': 'application/json' });

    if(this.localStorage.token !== null) {
      headers.append('Authorization', 'Bearer ' + this.localStorage.token);
    }

    if(params != null) {
      _.forEach(params, (value: string, key: string) => {
        headers.append(key, value);
      });
    }

    return headers;
  }

  private handleResponse(response: any) {
    this.isLoad = false;

    return response;
  }

  private handleError(request: Request, error: any): Promise<any> {
    this.isLoad = false;

    let handler: Function;
    _.forEach(this.errorHandlerInjectors, (value) => {
      if(value.error == error.json().error) {
        handler = value.handler;
        return false;
      }
    });

    return handler ? handler(request, error) : Promise.reject(error);
  }

  public get(request: Request): Promise<Response> {
    this.isLoad = true;

    let headers = this.makeHeader(request.header);
    request.method = RequestMethod.Get;

    return this.http.get(this.generateApiUrl(request.url, request.params), new RequestOptions({ headers })).toPromise()
    .then(this.handleResponse.bind(this))
    .catch(this.handleError.bind(this, request));
  }

  public post(request: Request): Promise<Response> {
    this.isLoad = true;

    let headers = this.makeHeader(request.header);
    request.method = RequestMethod.Post;

    return this.http.post(this.generateApiUrl(request.url), request.body, new RequestOptions({ headers })).toPromise()
    .then(this.handleResponse.bind(this))
    .catch(this.handleError.bind(this));
  }

}
