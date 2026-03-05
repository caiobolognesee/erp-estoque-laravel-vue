export type Product = {
  id: number;
  name: string;
  avg_cost: number;
  sale_price: number;
  stock: number;
};

export type PurchasePayload = {
  supplier: string;
  items: Array<{ product_id: number; quantity: number; unit_price: number }>;
};

export type SalePayload = {
  customer: string;
  items: Array<{ product_id: number; quantity: number; unit_price: number }>;
};

export type SaleResponse = {
  id: number;
  customer: string;
  total: number;
  profit: number;
  created_at: string;
  items: Array<{
    product_id: number;
    quantity: number;
    unit_price: number;
    cost_at_sale: number;
  }>;
};

export type Purchase = {
  id: number;
  supplier: string;
  created_at: string;
  items: Array<{ product_id: number; quantity: number; unit_price: number }>;
};

export type SaleListItem = {
  id: number;
  customer: string;
  total: number;
  profit: number;
  status: string;
  created_at: string;
  items: Array<{ product_id: number; quantity: number; unit_price: number; cost_at_sale: number }>;
};