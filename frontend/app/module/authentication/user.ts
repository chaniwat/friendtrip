export type Gender = "MALE" | "FEMALE";

export class User {
  id: number;
  name: string;
  email: string;
  gender: Gender;
  phone: string;
  created_at: string;
  updated_at: string;
}
