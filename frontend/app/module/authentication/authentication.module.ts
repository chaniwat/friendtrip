import { NgModule } from '@angular/core';
import { FormsModule } from "@angular/forms";
import { CommonModule } from "@angular/common";

import { UserService } from "./user.service";

import { authRouting } from "./routing";
import { AuthGuardService } from './auth-guard.service';
import { LoginComponent } from "./login.component";
import { RegisterComponent } from "./register.component";

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    authRouting
  ],
  declarations: [
    LoginComponent,
    RegisterComponent
  ],
  exports: [
    LoginComponent,
    RegisterComponent
  ],
  providers: [
    UserService,
    AuthGuardService
  ],
})
export class AuthenticationModule {}
