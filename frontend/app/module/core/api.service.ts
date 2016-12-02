import { Injectable } from '@angular/core';
import { Http, RequestMethod, Response, RequestOptions } from '@angular/http';
import { environment } from '../../../environments/environment';

import { LocalStorageService } from './localstorage.service';

import 'rxjs/add/operator/toPromise';
import { Observable } from "rxjs";

export class Request {
  constructor(
    public url: string,
    public method: RequestMethod,
    public params?: any,
    public header?: any
  ) { }
}

export class ErrorResponse {
  constructor(
    public message: string,
    public code?: number
  ) { }
}

/**
 * API Adapter
 */
@Injectable()
export class ApiService {

  // Static variable
  private readonly apiUrl: string = environment.protocol + '://' + environment.domain + '/api/';

  // Embedded Handlers
  private embeddedErrorHandlers: any = {};

  // Api State
  private isWaitingResponse: boolean = false;

  constructor(
    private http: Http,
    private localStorage: LocalStorageService
  ) { }

  /**
   * Add error handler (hook catch on error message)
   * @param error error message to be hooked
   * @param handler Function(request: {@link Request}, error: any), function to handle the error,
   */
  public addErrorHandler(error: string, handler: Function) {
    this.embeddedErrorHandlers[error] = handler;
  }

  /**
   * Is sending request (or waiting response)
   * @returns {boolean}
   */
  public isRequesting(): boolean {
    return this.isWaitingResponse;
  }

  /**
   * Generate API url for requesting
   * @param path
   * @param params add parameters to url (Only use with GET)
   * @returns {string}
   */
  public generateApiUrl(path: string, params?: any): string {
    let urlParam: string;
    if(params != null) {
      urlParam = '?';

      let paramCount = 0;
      _.forEach(params, (value: any, key: any) => {
        if(value != null && value != undefined) {
          if(paramCount > 0) {
            urlParam += '&';
          }
          urlParam += `${key}=${value}`;

          paramCount++;
        }
      });
    }

    return this.apiUrl + path + (params ? urlParam : '');
  }

  /**
   * @internal
   * Make header for requesting APIs
   * @param params
   * @returns any
   */
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

  /**
   * Request to API with GET method
   * @param url
   * @param params
   * @param headers
   * @returns {Promise<Response>}
   */
  public get(url: string, params?: any, headers?: any): Promise<any> {
    return this.request(new Request(url, RequestMethod.Get, params, headers));
  }

  /**
   * Request to API with POST method
   * @param url
   * @param params
   * @param headers
   * @returns {Promise<Response>}
   */
  public post(url: string, params?: any, headers?: any): Promise<any> {
    return this.request(new Request(url, RequestMethod.Post, params, headers));
  }

  /**
   * Request to API with PUT method
   * @param url
   * @param params
   * @param headers
   * @returns {Promise<Response>}
   */
  public put(url: string, params?: any, headers?: any): Promise<any> {
    return this.request(new Request(url, RequestMethod.Put, params, headers));
  }

  /**
   * Request to API with PATCH method
   * @param url
   * @param params
   * @param headers
   * @returns {Promise<Response>}
   */
  public patch(url: string, params?: any, headers?: any): Promise<any> {
    return this.request(new Request(url, RequestMethod.Patch, params, headers));
  }

  /**
   * Request to API with DELETE Method
   * @param url
   * @param params
   * @param headers
   * @returns {Promise<Response>}
   */
  public delete(url: string, params?: any, headers?: any): Promise<any> {
    return this.request(new Request(url, RequestMethod.Delete, params, headers));
  }

  /**
   * Request to API (Need to specified method)
   * @param request {@link Request} object with specified method
   * @returns {Promise<any>}
   */
  public request(request: Request): Promise<any> {
    this.isWaitingResponse = true;

    let requestOptions: RequestOptions = new RequestOptions({ headers: this.makeHeader(request.header) });

    let requester: Observable<any>;
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

  /**
   * @internal
   * Handle any success response
   * @param response
   * @returns {Promise<any>}
   */
  private handleResponse(response: any): Promise<any> {
    this.isWaitingResponse = false;

    return response.json();
  }

  /**
   * @internal
   * Handle any error response or failure request
   * @param request
   * @param error
   * @returns {any}
   */
  private handleError(request: Request, error: any): Promise<ErrorResponse> {
    this.isWaitingResponse = false;
    let errorResponse: ErrorResponse = new ErrorResponse(error.json().message, error.status);

    // find hooked error handler
    if(errorResponse.message in this.embeddedErrorHandlers) {
      return this.embeddedErrorHandlers[error](request, error);
    }

    return Promise.reject(errorResponse);
  }

}
