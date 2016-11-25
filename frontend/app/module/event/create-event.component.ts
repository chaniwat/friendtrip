import { Component, OnInit, AfterViewInit, OnDestroy } from '@angular/core';
import { NgForm } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';

import { ApiService } from '../core';

import { Event, EventType } from './event';
import { EventService } from './event.service';

@Component({
  selector: 'page-event-create',
  templateUrl: 'create-event.component.html',
  styleUrls: ['create-event.component.scss']
})
export class CreateEventComponent implements OnInit, AfterViewInit, OnDestroy {
  private submitted: boolean = false;
  private editor: any;

  eventTypes: EventType[];

  constructor(
    private apiService: ApiService,
    private eventService: EventService,
    private router: Router,
    private route: ActivatedRoute
  ) { }

  ngOnInit() {
    this.route.data.forEach((data: { types: EventType[] }) => {
      this.eventTypes = data.types;
    });
  }

  ngAfterViewInit() {
    tinymce.init({
      selector: '#event-detail',
      theme: 'inlite',
      skin_url: '/assets/skins/lightgray',
      plugins: 'image table link paste contextmenu textpattern autolink textcolor',
      insert_toolbar: 'quickimage quicktable | quicklink h1 h2 h3 h4 h5 h6 blockquote',
      selection_toolbar: 'bold italic underline strikethrough | alignleft aligncenter alignright | forecolor backcolor | quicklink h1 h2 h3 h4 h5 h6 blockquote',
      image_dimensions: false,
      image_class_list: [
        {title: 'None', value: ''},
        {title: 'Responsive', value: 'img-fluid'},
      ],
      image_advtab: true,
      images_upload_url: this.apiService.generateApiUrl('/images'),
      images_upload_base_path: this.apiService.generateApiUrl('/upload'),
      inline: true,
      paste_data_images: true,

      setup: editor => {
        this.editor = editor;
      }
    });
  }

  ngOnDestroy() {
    tinymce.remove(this.editor);
  }

  onSubmit(f: NgForm) {
    if(this.submitted) {
      return;
    }
    this.submitted = true;

    let event: Event = _.omit(f.value, ['date', 'time', 'length', 'start_time', 'end_time']) as Event;
    event.details = this.editor.getContent({format: 'raw'});
    // Parse to momentJS
    event.start_date = moment($("#event-start_date").val() + " " + $("#event-start_time").val(), ['DD/MM/YYYY h:mm A']);
    event.end_date = moment($("#event-end_date").val() + " " + $("#event-end_time").val(), ['DD/MM/YYYY h:mm A']);
    // Format
    event.start_date = event.start_date.format('YYYY-MM-DD HH:mm:ss');
    event.end_date = event.end_date.format('YYYY-MM-DD HH:mm:ss');

    // console.log(JSON.parse(JSON.stringify(event)));
    // this.submitted = false;

    this.editor.uploadImages(() => {
      this.eventService.createEvent(event)
      .then(result => {
        if(result) {
          toastr.success('Create new event successful');
          this.router.navigateByUrl('/event');
        }
      })
      .catch(error => {
        toastr.error('Something wrong: ' + error);
        this.submitted = false;
      });
    });
  }
}
