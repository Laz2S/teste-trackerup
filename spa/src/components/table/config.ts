const paginationLabel = (x: string, y: string, z: string) => `Mostrando de ${x} a ${y} Total: ${z}`;
const getSelectedString = (x: number) => (x > 1 ? `${x} linhas selecionadas` : `${x} linha selecionada`);
const rowsPerPageLabel = 'Registros por pagina';
const noDataLabel = 'Sem registros';
const loadingLabel = 'Carregando';
const rowsPerPageOptions = [10, 20, 25, 50, 100];

export default {
  paginationLabel,
  rowsPerPageLabel,
  getSelectedString,
  noDataLabel,
  loadingLabel,
  rowsPerPageOptions,
};
