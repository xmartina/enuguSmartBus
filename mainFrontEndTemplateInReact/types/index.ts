export interface User {
  id: string;
  name: string;
  email: string;
  avatar?: string;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
  error?: string;
}

export type Status = 'idle' | 'loading' | 'success' | 'error';
