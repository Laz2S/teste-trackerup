export interface Todo {
  id: number;
  content: string;
}
export interface Meta {
  totalCount: number;
}
export interface Category {
  id: number;
  name: string;
}
export interface Product {
  id: number;
  name: string;
  identifier: string;
  category: Category;
  category_id: number;
}
export interface Params extends Pagination {
  term: string;
}
export interface Props {
  pagination: Pagination;
}
export interface Pagination {
  sortBy: string;
  descending: boolean;
  rowsPerPage: number;
  page: number;
  rowsNumber: number;
}
export interface Option {
  id: string | number;
  name: string;
}
export interface Select {
  options: Array<Option>;
  selected: Option;
}