import { Injectable } from '@angular/core';

import { Event, EventType } from './event';
import { ApiService } from '../core';
import { Pagination } from "../../utility/pagination";

@Injectable()
export class EventService {

  constructor(
    private api: ApiService
  ) { }

  /**
   * Create new event
   */
  public createEvent(event: Event): Promise<boolean> {
    return this.api.post('events', { event })
      .then(() => true)
  }

  /**
   * Get all events
   */
  public getEvents(page?: number): Promise<{data: Event[], pagination: Pagination}> {
    return this.api.get('events', { page })
      .then(response => { return {
        data: response.events as Event[],
        pagination: response.pagination as Pagination
      }});
  }

  /**
   * Get event detail
   */
  public getEvent(id: number): Promise<Event> {
    return this.api.get(`events/${id}`)
      .then(response => response as Event)
  }

  /**
   * Get all event types
   */
  public getTypes(): Promise<EventType[]> {
    return this.api.get('events/types')
      .then(response => response.types as EventType[])
  }

}
