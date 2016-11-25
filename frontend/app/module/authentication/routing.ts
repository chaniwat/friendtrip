import { ModuleWithProviders }  from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { RegisterComponent } from "./register.component";
import { LoginComponent } from "./login.component";

const appRoutes: Routes = [
  {
    path: 'login',
    component: LoginComponent
  },
  {
    path: 'register',
    component: RegisterComponent
  },
];

export const authRouting: ModuleWithProviders = RouterModule.forChild(appRoutes);
