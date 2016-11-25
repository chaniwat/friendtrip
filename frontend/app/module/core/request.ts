import { Headers, RequestMethod } from '@angular/http';

export class Request {

  constructor(
    public url: string,
    public params?: any,
    public body?: string,
    public method?: RequestMethod,
    public header?: any
  ) { }

}
