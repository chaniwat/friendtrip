import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

import { Event } from './event';
import { EventService } from './event.service';
import { Pagination } from "../../utility/pagination";

@Component({
  selector: 'page-event',
  templateUrl: 'event.component.html',
  styleUrls: ['event.component.scss']
})
export class EventComponent implements OnInit {

  events: Event[];
  pagination: Pagination;

  constructor(
    private eventService: EventService,
    private route: ActivatedRoute
  ) { }

  ngOnInit() {
    this.route.data.forEach((data: { events: {data: Event[], pagination: Pagination} }) => {
      this.events = data.events.data;
      this.pagination = data.events.pagination;
    });
  }

  public get numsPage(): number[] {
    let numArray = [];

    for(let i = 1; i <= this.pagination.last_page; i++) {
      numArray.push(i);
    }

    return numArray;
  }

  public nextPage() {
    if(this.pagination.next_page_url != null) {
      return this.toPage(this.pagination.current_page + 1);
    }
  }

  public toPage(page: number) {
    return this.eventService.getEvents(page)
      .then(response => {
        this.events = response.data;
        this.pagination = response.pagination;
      });
  }

  public previousPage() {
    if(this.pagination.prev_page_url != null) {
      return this.toPage(this.pagination.current_page - 1);
    }
  }

  public isCurrentPage(page: number) {
    return page == this.pagination.current_page;
  }

}
