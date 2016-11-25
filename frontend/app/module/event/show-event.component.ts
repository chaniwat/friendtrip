import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

import { Event } from './event';
import { EventService } from './event.service';

import { UserService } from '../authentication';

@Component({
  selector: 'page-event-show',
  templateUrl: 'show-event.component.html',
})
export class ShowEventComponent implements OnInit {

  event: Event;

  constructor(
    private eventService: EventService,
    private route: ActivatedRoute,
    private sanitizer: DomSanitizer,
    private userService: UserService
  ) { }

  ngOnInit() {
    this.route.data.forEach((data: { event: Event }) => {
      this.event = data.event;
    });

    if(!this.userService.isLoggedIn()) {
      toastr.warning('กรุณาเข้าสู่ระบบก่อนร่วมกิจกรรม');
    }
  }

  public get eventDetails(): SafeHtml {
    return this.sanitizer.bypassSecurityTrustHtml(this.event.details);
  }

}
