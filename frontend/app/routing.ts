import { ModuleWithProviders }  from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { HomeComponent } from './component';

const appRoutes: Routes = [
  {
    path: '',
    component: HomeComponent
  },
  {
    path: 'event',
    loadChildren: './module/event/index#EventModule'
  }
];

export const routing: ModuleWithProviders = RouterModule.forRoot(appRoutes);
