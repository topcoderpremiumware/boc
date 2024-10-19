import React, { useState } from "react";
import axios from "axios";

// Example data for the products
const products = [
  {
    id: 1,
    name: "BG Berlin Ted Cabin Luggage",
    price: 74.50,
    oldPrice: 149.00,
    img: "https://example.com/image1.jpg", // Replace with your image URL
    discount: 50,
    colors: ["black", "pink", "blue"],
  },
  {
    id: 2,
    name: "School Trouser Unisex",
    price: 7.90,
    oldPrice: null,
    img: "https://example.com/image2.jpg",
    colors: ["black", "navy", "gray"],
  },
  {
    id: 3,
    name: "Adidas Neo Women Shoes",
    price: 34.95,
    oldPrice: 69.95,
    img: "https://example.com/image3.jpg",
    discount: 50,
    colors: ["white", "gold"],
  },
  // Add more products as needed...
];

const Shop = () => {
  const [cart, setCart] = useState([]);

  const addToCart = (product) => {
    setCart([...cart, product]);
    alert(`${product.name} has been added to your cart.`);
  };

  const buyByBOC = async (product) => {
    try {
      // API URL for getting the token
      const tokenUrl = "https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/oauth2/token";
      
      // Credentials (replace these with your actual client id and secret)
      const client_id = "4c13ca5d5234603dfb3228c381d7d3ac";
      const client_secret = "4c13ca5d5234603dfb3228c381d7d3ac";
      
      // Make the API call to get the OAuth token
      const response = await axios.post(tokenUrl, null, {
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        data: `grant_type=client_credentials&client_id=${client_id}&client_secret=${client_secret}&scope=TPPOAuth2Security`
      });

      // Extract token from response
      const accessToken = response.data.access_token;

      // You can now use the token to call other BOC APIs.
      alert(`Proceeding to buy ${product.name} by BOC with token: ${accessToken}`);
      
      // Perform further actions like calling other BOC APIs with the token
      // Example API call using the token can go here

    } catch (error) {
      console.error("Error fetching token:", error);
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
