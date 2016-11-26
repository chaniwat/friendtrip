import { Injectable } from '@angular/core';

import { Event, EventType } from './event';
import { ApiService } from '../core';

@Injectable()
export class EventService {

  constructor(
    private api: ApiService
  ) { }

  /**
  * Create new event
  */
  public createEvent(event: Event): Promise<boolean> {
    return this.api.post('/events', { event })
    .then(response => { return response.status == 201; })
  }

  /**
   * Get all events
   */
  public getEvents(page?: number): Promise<{data: Event[], pagination: any}> {
    return this.api.get('/events', { page })
    .then(response => {
      return {
        data: response.json().events as Event[],
        pagination: response.json().pagination
      };
    })
  }

  /**
   * Get single event
   */
  public getEvent(id: number): Promise<Event> {
    return this.api.get('/events/' + id)
    .then(response => response.json() as Event)
  }

  /**
  * Get all event types
  */
  public getTypes(): Promise<EventType[]> {
    return this.api.get('/events/types')
    .then(response => response.json().types as EventType[])
  }

}
