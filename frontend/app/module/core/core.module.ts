import { NgModule } from '@angular/core';

import { ApiService } from './api.service';
import { LocalStorageService } from './localstorage.service';

@NgModule({
  providers: [
    ApiService,
    LocalStorageService
  ]
})
export class CoreModule {}
