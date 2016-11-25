import { User } from '../authentication';

export type EventState = "ON" | "CANCEL";

export class Event {
  id: number;
  name: string;
  type: EventType|number;
  owner: User;
  destination: string;
  appointment_place: string;
  start_date: moment.Moment|string;
  end_date: moment.Moment|string;
  approximate_cost: number;
  details: string;
  state: EventState;
  settings: EventSetting[];
  join: boolean|string;
}

export class EventType {
  id: number;
  name: string;
  detail: string;
}

export class EventSetting {
  key: string;
  value: string;
}
