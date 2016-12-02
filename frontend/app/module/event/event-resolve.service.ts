import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot } from '@angular/router';

import { Event } from './event';
import { EventService } from './event.service';
import { Pagination } from "../../utility/pagination";

@Injectable()
export class EventResolve implements Resolve<{data: Event[], pagination: any}> {

  constructor(
    private eventService: EventService,
  ) { }

  resolve(route: ActivatedRouteSnapshot): Promise<{data: Event[], pagination: Pagination}> {
    return this.eventService.getEvents()
    .then(result => result);
  }

}
