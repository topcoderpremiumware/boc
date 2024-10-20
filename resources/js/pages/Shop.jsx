import React, { useState } from "react";
import axios from "axios";

const products = [
  {
    id: 1,
    name: "Samsonite Winfield 2 Hardside Luggage",
    price: 199.99,
    oldPrice: 249.99,
    img: "https://m.media-amazon.com/images/I/71Hdzva20uL.jpg", // Updated
    discount: 20,
    colors: ["black", "silver", "blue"],
  },
  {
    id: 2,
    name: "Levi's Men's 501 Original Fit Jeans",
    price: 49.99,
    oldPrice: 59.99,
    img: "https://m.media-amazon.com/images/I/415aQwqvBQL._AC_.jpg", // Updated
    discount: 16,
    colors: ["blue", "black", "gray"],
  },
  {
    id: 3,
    name: "Nike Air Max 270",
    price: 149.95,
    oldPrice: 170.00,
    img: "https://www.jdsports.cy/2667732-product_horizontal/nike-w-air-max-270.jpg", // Updated
    discount: 12,
    colors: ["white", "black", "red"],
  },
  {
    id: 4,
    name: "Apple MacBook Air 13-inch",
    price: 999.00,
    oldPrice: 1099.00,
    img: "https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/mba13-midnight-select-202402?wid=904&hei=840&fmt=jpeg&qlt=90&.v=1708367688034", // Updated
    discount: 9,
    colors: ["silver", "space gray", "gold"],
  },
  {
    id: 5,
    name: "Sony WH-1000XM5 Wireless Headphones",
    price: 349.99,
    oldPrice: 399.99,
    img: "https://m.media-amazon.com/images/I/61ULAZmt9NL.jpg", // Updated
    discount: 12,
    colors: ["black", "silver"],
  },
  {
    id: 6,
    name: "Canon EOS Rebel T7 DSLR Camera",
    price: 479.00,
    oldPrice: 549.00,
    img: "https://m.media-amazon.com/images/I/71Is-Zv6A0L._AC_UF894,1000_QL80_.jpg", // Updated
    discount: 13,
    colors: ["black"],
  },
  {
    id: 7,
    name: "Fossil Men's Nate Stainless Steel Chronograph Watch",
    price: 129.99,
    oldPrice: 149.99,
    img: "https://m.media-amazon.com/images/I/71kbRVr8YfL._AC_UY900_.jpg", // Updated
    discount: 13,
    colors: ["black", "silver"],
  },
  {
    id: 8,
    name: "Dyson V11 Torque Drive Cordless Vacuum Cleaner",
    price: 599.99,
    oldPrice: 699.99,
    img: "https://m.media-amazon.com/images/I/61-K1LeoE5L.jpg", // Updated
    discount: 14,
    colors: ["blue"],
  },
  {
    id: 9,
    name: "Samsung 55-inch Class QLED Q60T Smart TV",
    price: 497.99,
    oldPrice: 599.99,
    img: "https://m.media-amazon.com/images/I/71RspW4qsqL.jpg", // Updated
    discount: 17,
    colors: ["black"],
  },
  {
    id: 10,
    name: "Adidas Ultraboost 21 Running Shoes",
    price: 179.95,
    oldPrice: 200.00,
    img: "https://www.runningshoesguru.com/wp-content/uploads/2021/02/Adidas-Ultraboost-21-IMG_1791.jpeg", // Updated
    discount: 10,
    colors: ["white", "black", "gray"],
  },
];

const Shop = () => {
  const [cart, setCart] = useState([]);

  const addToCart = (product) => {
    setCart([...cart, product]);
    alert(`${product.name} has been added to your cart.`);
  };

  const buyByBOC = async (product) => {
      window.location.href = `/?price=${product.price}&name=${product.name}`
  };
  

  return (
    <div style={{ display: "flex", flexWrap: "wrap", justifyContent: "space-around" }}>
      {products.map((product) => (
        <div
          key={product.id}
          style={{
            border: "1px solid #ccc",
            padding: "16px",
            width: "200px",
            margin: "10px",
            textAlign: "center",
          }}
        >
         <img
            src={product.img}
            alt={product.name}
            style={{ width: "100%", height: "150px", objectFit: "contain" }}
            onError={() => console.error(`Image failed to load: ${product.img}`)} // Log error if image fails to load
          />
          <h3>{product.name}</h3>
          {product.oldPrice && (
            <p style={{ textDecoration: "line-through" }}>€{product.oldPrice}</p>
          )}
          <p style={{ color: "red" }}>€{product.price}</p>
          {product.discount && <p>-{product.discount}%</p>}
          <div>
            {product.colors.map((color, idx) => (
              <span key={idx} style={{ margin: "0 5px" }}>
                {color}
              </span>
            ))}
          </div>
          <button
            onClick={() => addToCart(product)}
            style={{
              margin: "10px 0",
              padding: "10px",
              backgroundColor: "#28a745",
              color: "white",
              border: "none",
              cursor: "pointer",
            }}
          >
            Add to Cart
          </button>
          <button
            onClick={() => buyByBOC(product)}
            style={{
              margin: "10px 0",
              padding: "10px",
              backgroundColor: "#007bff",
              color: "white",
              border: "none",
              cursor: "pointer",
            }}
          >
            Buy by BOC
          </button>
        </div>
      ))}
    </div>
  );
};

export default Shop;
