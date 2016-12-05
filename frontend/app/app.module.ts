import { NgModule, APP_INITIALIZER } from '@angular/core';
import { HttpModule } from '@angular/http';
import { BrowserModule } from '@angular/platform-browser';

import { CoolStorageModule } from 'angular2-cool-storage';

import { CoreModule } from "./module/core/index";
import { AuthenticationModule, UserService } from "./module/authentication";
import { EventModule } from "./module/event";
import { routing } from "./routing";

import { AppComponent } from './app.component';
import { HomeComponent, NavbarComponent } from './component';
import { ReactiveFormsModule, FormsModule } from "@angular/forms";
import { FooterComponent } from "./component/footer.component";

@NgModule({
  imports: [
    BrowserModule,
    HttpModule, // HTTP Service
    CoolStorageModule, // HTML5 Local Storage
    FormsModule,
    ReactiveFormsModule, // Reactive form
    CoreModule,
    AuthenticationModule,
    EventModule,
    routing
  ],
  declarations: [
    AppComponent,
    HomeComponent,
    NavbarComponent,
    FooterComponent
  ],
  providers: [
    { provide: APP_INITIALIZER,
      useFactory: (userService: UserService) => () => {
        // ignore any error (only resolve the user)
        return userService.getAuthenticatedUser()
          .then(() => true)
          .catch(() => Promise.resolve(true))
      },
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
      preventDuplicates: true,
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
