import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot } from '@angular/router';

import { Event } from './event';
import { EventService } from './event.service';

@Injectable()
export class EventDetailResolve implements Resolve<Event> {

  constructor(
    private eventService: EventService,
  ) { }

  resolve(route: ActivatedRouteSnapshot): Promise<Event> {
    let id = +route.params['id'];

    return this.eventService.getEvent(id)
    .then(response => response);
  }

}
