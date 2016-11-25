import { Injectable } from '@angular/core';
import { Router, Resolve, ActivatedRouteSnapshot } from '@angular/router';

import { EventType } from './event';
import { EventService } from './event.service';

@Injectable()
export class EventTypeResolve implements Resolve<EventType[]> {

  constructor(
    private eventService: EventService,
    private router: Router
  ) { }

  resolve(route: ActivatedRouteSnapshot): Promise<EventType[]> {
    return this.eventService.getTypes()
    .then(result => result);
  }

}
