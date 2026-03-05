const BRL = new Intl.NumberFormat("pt-BR", {
  style: "currency",
  currency: "BRL",
});

export function formatBRL(value: number | string | null | undefined): string {
  const num = Number(value);

  if (!Number.isFinite(num)) {
    return BRL.format(0);
  }

  return BRL.format(num);
}