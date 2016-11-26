import { NgModule, APP_INITIALIZER } from '@angular/core';
import { HttpModule } from '@angular/http';
import { BrowserModule } from '@angular/platform-browser';

import { CoolStorageModule } from 'angular2-cool-storage';

import { CoreModule } from "./module/core/index";
import { routing } from "./routing";

import { AppComponent } from './app.component';
import { HomeComponent, NavbarComponent, FooterComponent } from './component';

import { AuthenticationModule, UserService } from "./module/authentication";

@NgModule({
  imports: [
    BrowserModule,
    HttpModule, // HTTP Service
    CoolStorageModule, // HTML5 Local Storage
    CoreModule,
    AuthenticationModule,
    routing
  ],
  declarations: [
    AppComponent,
    HomeComponent,
    NavbarComponent,
    FooterComponent,
  ],
  providers: [
    { provide: APP_INITIALIZER,
      useFactory: (userService: UserService) => () => userService.getAuthenticatedUser(),
      deps: [UserService],
      multi: true }
  ],
  bootstrap: [AppComponent]
})
export class AppModule {

  constructor() {
    // TODO Make own notification system (Toast)
    toastr.options = {
      closeButton: false,
      debug: false,
      newestOnTop: true,
      progressBar: true,
      positionClass: "toast-bottom-left",
      preventDuplicates: false,
      onclick: null,
      showDuration: 300,
      hideDuration: 1000,
      timeOut: 5000,
      extendedTimeOut: 1000,
      showEasing: "swing",
      hideEasing: "linear",
      showMethod: "fadeIn",
      hideMethod: "fadeOut"
    } as ToastrOptions;
  }

}
