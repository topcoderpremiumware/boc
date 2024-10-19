import React, { useState } from "react";
import axios from "axios";

const products = [
  {
    id: 1,
    name: "Samsonite Winfield 2 Hardside Luggage",
    price: 199.99,
    oldPrice: 249.99,
    img: "https://m.media-amazon.com/images/I/81clKJPeHtL._AC_SL1500_.jpg", // Updated
    discount: 20,
    colors: ["black", "silver", "blue"],
  },
  {
    id: 2,
    name: "Levi's Men's 501 Original Fit Jeans",
    price: 49.99,
    oldPrice: 59.99,
    img: "https://m.media-amazon.com/images/I/81z1SQ4J3cL._AC_UL1500_.jpg", // Updated
    discount: 16,
    colors: ["blue", "black", "gray"],
  },
  {
    id: 3,
    name: "Nike Air Max 270",
    price: 149.95,
    oldPrice: 170.00,
    img: "https://m.media-amazon.com/images/I/61cS0cF7yXL._AC_UL1500_.jpg", // Updated
    discount: 12,
    colors: ["white", "black", "red"],
  },
  {
    id: 4,
    name: "Apple MacBook Air 13-inch",
    price: 999.00,
    oldPrice: 1099.00,
    img: "https://m.media-amazon.com/images/I/81Yj49MZDhL._AC_SL1500_.jpg", // Updated
    discount: 9,
    colors: ["silver", "space gray", "gold"],
  },
  {
    id: 5,
    name: "Sony WH-1000XM5 Wireless Headphones",
    price: 349.99,
    oldPrice: 399.99,
    img: "https://m.media-amazon.com/images/I/81sB1l4wQjL._AC_SL1500_.jpg", // Updated
    discount: 12,
    colors: ["black", "silver"],
  },
  {
    id: 6,
    name: "Canon EOS Rebel T7 DSLR Camera",
    price: 479.00,
    oldPrice: 549.00,
    img: "https://m.media-amazon.com/images/I/81qHW7bLl5L._AC_SL1500_.jpg", // Updated
    discount: 13,
    colors: ["black"],
  },
  {
    id: 7,
    name: "Fossil Men's Nate Stainless Steel Chronograph Watch",
    price: 129.99,
    oldPrice: 149.99,
    img: "https://m.media-amazon.com/images/I/81aP+63ibcL._AC_SL1500_.jpg", // Updated
    discount: 13,
    colors: ["black", "silver"],
  },
  {
    id: 8,
    name: "Dyson V11 Torque Drive Cordless Vacuum Cleaner",
    price: 599.99,
    oldPrice: 699.99,
    img: "https://m.media-amazon.com/images/I/61ZRmB9DqUL._AC_SL1500_.jpg", // Updated
    discount: 14,
    colors: ["blue"],
  },
  {
    id: 9,
    name: "Samsung 55-inch Class QLED Q60T Smart TV",
    price: 497.99,
    oldPrice: 599.99,
    img: "https://m.media-amazon.com/images/I/81Hhrm63NCL._AC_SL1500_.jpg", // Updated
    discount: 17,
    colors: ["black"],
  },
  {
    id: 10,
    name: "Adidas Ultraboost 21 Running Shoes",
    price: 179.95,
    oldPrice: 200.00,
    img: "https://m.media-amazon.com/images/I/81U3FZ1EbFL._AC_SL1500_.jpg", // Updated
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
    try {
      const tokenUrl = "https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/oauth2/token";
      const client_id = "4c13ca5d5234603dfb3228c381d7d3ac";
      const client_secret = "4c13ca5d5234603dfb3228c381d7d3ac";
  
      const requestBody = new URLSearchParams();
      requestBody.append("grant_type", "client_credentials");
      requestBody.append("client_id", client_id);
      requestBody.append("client_secret", client_secret);
      requestBody.append("scope", "TPPOAuth2Security");
  
      const response = await axios.post(tokenUrl, requestBody.toString(), {
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      });
  
      const accessToken = response.data.access_token;
  
      if (!accessToken) {
        throw new Error("No access token received.");
      }
  
      alert(`Proceeding to buy ${product.name} by BOC with token: ${accessToken}`);
  
      // Example API call using the token can go here
  
    } catch (error) {
      console.error("Error fetching token:", error.response ? error.response.data : error.message);
      alert(`Failed to proceed with BOC purchase: ${error.message}`);
    }
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
