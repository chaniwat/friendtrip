export type Gender = "MALE" | "FEMALE";

export class User {
  id: number;
  email: string;
  first_name: string;
  last_name: string;
  display_name: string;
  birthdate: string;
  gender: Gender;
  religion: string;
  phone: string;
  admin: boolean;
}
