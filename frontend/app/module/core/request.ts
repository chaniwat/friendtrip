import { RequestMethod } from '@angular/http';

export class Request {

  constructor(
    public url: string,
    public method: RequestMethod,
    public params?: any,
    public header?: any
  ) { }

}
