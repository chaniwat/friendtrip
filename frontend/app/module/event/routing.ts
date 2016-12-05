import { ModuleWithProviders }  from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { AuthGuardService } from '../authentication';

import { EventComponent} from './event.component';
import { ShowEventComponent } from './show-event.component';
import { CreateEventComponent } from "./create-event.component";

import { EventResolve } from './event-resolve.service';
import { EventDetailResolve } from './event-detail-resolve.service';
import { EventTypeResolve } from './event-types-resolve.service';

const appRoutes: Routes = [
  {
    path: 'event',
    component: EventComponent,
    resolve: {
      events: EventResolve
    },
    children: [
      {
        path: 'create',
        component: CreateEventComponent,
        canActivate: [AuthGuardService],
        resolve: {
          types: EventTypeResolve
        }
      },
      {
        path: ':id',
        component: ShowEventComponent,
        resolve: {
          event: EventDetailResolve
        }
      }
    ]
  }
];

export const eventRouting: ModuleWithProviders = RouterModule.forChild(appRoutes);
