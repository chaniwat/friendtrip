import { NgModule } from '@angular/core';
import { CommonModule } from "@angular/common";
import { FormsModule } from "@angular/forms";

import { eventRouting } from "./routing";

import { EventService } from './event.service';
import { EventResolve } from './event-resolve.service';
import { EventDetailResolve } from './event-detail-resolve.service';
import { EventTypeResolve } from './event-types-resolve.service';

import { EventComponent } from './event.component';
import { ShowEventComponent } from './show-event.component';
import { CreateEventComponent } from "./create-event.component";

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    eventRouting
  ],
  declarations: [
    EventComponent,
    ShowEventComponent,
    CreateEventComponent
  ],
  exports: [
    EventComponent,
    ShowEventComponent,
    CreateEventComponent
  ],
  providers: [
    EventService,
    EventResolve,
    EventDetailResolve,
    EventTypeResolve
  ]
})
export class EventModule {}
