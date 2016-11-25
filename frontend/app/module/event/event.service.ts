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
    let body = JSON.stringify({ event });

    return this.api.post(this.api.makeRequest('/events', body))
    .then(response => { return response.status == 201; })
    .catch(this.handleError.bind(this));
  }

  /**
   * Get all events
   */
  public getEvents(page?: number): Promise<{data: Event[], pagination: any}> {
    let params = {};

    if(page) {
      params = _.merge(params, { page });
    }

    return this.api.get(this.api.makeRequest('/events', params))
    .then(response => {
      return {
        data: response.json().events as Event[],
        pagination: response.json().pagination
      };
    })
    .catch(this.handleError.bind(this));
  }

  /**
   * Get single event
   */
  public getEvent(id: number): Promise<Event> {
    return this.api.get(this.api.makeRequest('/events/' + id))
    .then(response => response.json() as Event)
    .catch(this.handleError.bind(this));
  }

  /**
  * Get all event types
  */
  public getTypes(): Promise<EventType[]> {
    return this.api.get(this.api.makeRequest('/events/types'))
    .then(response => response.json().types as EventType[])
    .catch(this.handleError.bind(this))
  }

  /**
  * Handle any promises error
  */
  private handleError(error: any): Promise<any> {
    error = error.json().error;

    return Promise.reject(error);
  }
}
