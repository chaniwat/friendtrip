import { Injectable } from '@angular/core';
import { Router, Resolve, ActivatedRouteSnapshot } from '@angular/router';

import { Event } from './event';
import { EventService } from './event.service';

@Injectable()
export class EventResolve implements Resolve<{data: Event[], pagination: any}> {

  constructor(
    private eventService: EventService,
    private router: Router
  ) { }

  resolve(route: ActivatedRouteSnapshot): Promise<{data: Event[], pagination: any}> {
    return this.eventService.getEvents()
    .then(result => result);
  }

}
