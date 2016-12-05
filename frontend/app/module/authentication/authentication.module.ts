import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from "@angular/forms";
import { CommonModule } from "@angular/common";

import { UserService } from "./user.service";

import { authRouting } from "./routing";
import { AuthGuardService } from './auth-guard.service';
import { LoginComponent } from "./login.component";
import { RegisterComponent } from "./register.component";
import { LoginModalComponent } from "./login-modal.component";

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    authRouting
  ],
  declarations: [
    LoginComponent,
    RegisterComponent,
    LoginModalComponent
  ],
  exports: [
    LoginComponent,
    RegisterComponent,
    LoginModalComponent
  ],
  providers: [
    UserService,
    AuthGuardService
  ],
})
export class AuthenticationModule {}
