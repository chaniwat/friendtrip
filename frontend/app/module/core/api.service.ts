import { Injectable } from '@angular/core';
import { Headers, Http, RequestMethod, Response, RequestOptions } from '@angular/http';
import { environment } from '../../../environments/environment';

import { LocalStorageService } from './localstorage.service';
import { Request } from './request';

import 'rxjs/add/operator/toPromise';
import { Observable } from "rxjs";

/**
 * API Adapter
 */
@Injectable()
export class ApiService {

  // Static variable
  private readonly apiUrl: string = environment.protocol + '://' + environment.domain + '/api';

  // Embedded Handlers
  private embeddedErrorHandlers: any = {};

  // Api State
  private isWaitingResponse: boolean = false;

  constructor(
    private http: Http,
    private localStorage: LocalStorageService
  ) { }

  public embedErrorHandler(error: string, handler: Function) {
    this.embeddedErrorHandlers[error] = handler;
  }

  public isRequesting(): boolean {
    return this.isWaitingResponse;
  }

  public generateApiUrl(path: string, params?: any): string {
    let urlParam: string;
    if(params != null) {
      urlParam = '?';

      _.forEach(params, (value: any, key: any) => {
        if(value != null && value != undefined) {
          urlParam += `${key}=${value}`;
        }
      });
    }

    return this.apiUrl + path + (params ? urlParam : '');
  }

  private makeHeader(params?: any): any {
    let headers = {'Accept': 'application/json', 'Content-Type': 'application/json'};

    if(this.localStorage.token !== null) {
      headers['Authorization'] = 'Bearer ' + this.localStorage.token;
    }

    if(params != null) {
      _.forEach(params, (value: string, key: string) => {
        headers[key] = value;
      });
    }

    return headers;
  }

  public get(url: string, params?: any, headers?: any): Promise<Response> {
    return this.request(new Request(url, RequestMethod.Get, params, this.makeHeader(headers)));
  }

  public post(url: string, params?: any, headers?: any): Promise<Response> {
    return this.request(new Request(url, RequestMethod.Post, params, this.makeHeader(headers)));
  }

  public put(url: string, params?: any, headers?: any): Promise<Response> {
    return this.request(new Request(url, RequestMethod.Put, params, this.makeHeader(headers)));
  }

  public patch(url: string, params?: any, headers?: any): Promise<Response> {
    return this.request(new Request(url, RequestMethod.Patch, params, this.makeHeader(headers)));
  }

  public delete(url: string, params?: any, headers?: any): Promise<Response> {
    return this.request(new Request(url, RequestMethod.Delete, params, this.makeHeader(headers)));
  }

  public request(request: Request): Promise<Response> {
    this.isWaitingResponse = true;

    let requestOptions: RequestOptions = new RequestOptions({ headers: this.makeHeader(request.header) });

    let requester: Observable<Response>;
    if (request.method === RequestMethod.Get) {
      requester = this.http.get(this.generateApiUrl(request.url, request.params), requestOptions);
    } else if (request.method === RequestMethod.Post) {
      requester = this.http.post(this.generateApiUrl(request.url), JSON.stringify(request.params), requestOptions);
    } else if (request.method === RequestMethod.Put) {
      requester = this.http.put(this.generateApiUrl(request.url), JSON.stringify(request.params), requestOptions);
    } else if (request.method === RequestMethod.Patch) {
      requester = this.http.patch(this.generateApiUrl(request.url), JSON.stringify(request.params), requestOptions);
    } else if (request.method === RequestMethod.Delete) {
      requester = this.http.delete(this.generateApiUrl(request.url), requestOptions);
    }

    return requester.toPromise()
      .then(this.handleResponse.bind(this))
      .catch(this.handleError.bind(this, request));
  }

  private handleResponse(response: any): Promise<any> {
    this.isWaitingResponse = false;

    return response;
  }

  private handleError(request: Request, error: any): Promise<any> {
    this.isWaitingResponse = false;

    if((error = error.json().error) in this.embeddedErrorHandlers) {
      return this.embeddedErrorHandlers[error](request, error);
    }

    return Promise.reject(error);
  }

}
