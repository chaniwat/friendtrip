import { Component } from '@angular/core';

import { ApiService } from './module/core';

@Component({
  selector: 'main-app',
  templateUrl: 'app.component.html'
})
export class AppComponent {

  constructor(
    private api: ApiService
  ) {}

  public get isLoading(): boolean {
    return this.api.isLoading();
  }

}
