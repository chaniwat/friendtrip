import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

import { Event } from './event';
import { EventService } from './event.service';

@Component({
  selector: 'page-event',
  templateUrl: 'event.component.html',
  styleUrls: ['event.component.scss']
})
export class EventComponent implements OnInit {

  events: Event[];
  pagination: any;

  constructor(
    private eventService: EventService,
    private route: ActivatedRoute
  ) { }

  ngOnInit() {
    this.route.data.forEach((data: { events: {data: Event[], pagination: any} }) => {
      this.events = data.events.data;
      this.pagination = data.events.pagination;
    });
  }

  public get numArray(): number[] {
    let numArray = [];

    for(let i = 1; i <= this.pagination.last_page; i++) {
      numArray.push(i);
    }

    return numArray;
  }

  public paginationNext() {
    if(this.pagination.next_page_url != null) {
      this.eventService.getEvents(this.pagination.current_page + 1)
      .then(response => {
        this.events = response.data;
        this.pagination = response.pagination;
      });
    }
  }

  public paginationTo(page: number) {
    this.eventService.getEvents(page)
    .then(response => {
      this.events = response.data;
      this.pagination = response.pagination;
    });
  }

  public paginationPrev() {
    if(this.pagination.prev_page_url != null) {
      this.eventService.getEvents(this.pagination.current_page - 1)
      .then(response => {
        this.events = response.data;
        this.pagination = response.pagination;
      });
    }
  }

  public paginationIsCurrentPage(page: number) {
    return page == this.pagination.current_page;
  }

}
