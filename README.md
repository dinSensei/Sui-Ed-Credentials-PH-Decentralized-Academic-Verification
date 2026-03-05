# 🎓 Sui-Ed Credentials PH: Decentralized Academic Verification

> **Status: Ideation & Architecture Phase** > We are currently building the conceptual framework and architecture for this project. Developers, educators, and Web3 enthusiasts are welcome to contribute!

## 📜 Overview
The integrity of academic credentials in the Philippines faces systemic challenges—from the proliferation of "diploma mills" to the slow, manual verification processes required by employers and government agencies. 

**Sui-Ed Credentials PH** is an open-source, hybrid Web2/Web3 architecture designed to issue mathematically verifiable, tamper-proof academic credentials. By leveraging the Sui blockchain's high-speed, object-centric model, educational institutions can issue Soulbound Tokens (SBTs) to graduates. This system shifts the burden of trust from easily forged paper documents to an immutable decentralized ledger.

## 🎯 The Vision
Instead of reinventing the wheel, this project is designed to integrate seamlessly as a new module within existing school management systems. 

* **For Students:** Total ownership of their digital diplomas, securely held in a self-custodial Sui wallet.
* **For Employers:** Instant, zero-cost verification of a candidate's educational background.
* **For DepEd/CHED:** A modernized, automated registry where only whitelisted institutions (e.g., authorized JHS and SHS providers) hold the cryptographic authority to mint credentials.

## 🏗️ Hybrid Architecture 

### 1. The Web2 Layer (School Management Integration)
* **Frontend:** Standard HTML, CSS, and vanilla JavaScript dashboards for class advisers, registrars, and students.
* **Backend:** PHP handles the core business logic, user authentication, and data formatting.
* **Database:** A relational SQL database stores everyday, granular data (grades, attendance, daily schedules) to keep on-chain costs near zero.

### 2. The Middleware Layer
* **Sui TypeScript SDK:** A lightweight Node.js microservice bridges the PHP backend to the Sui network.
* **Data Hashing:** The PHP backend hashes the student's final Transcript of Records (using SHA-256) and passes only this lightweight string to the blockchain.

### 3. The Web3 Layer (Sui Network)
* **Smart Contracts (Sui Move):** Handles the creation of `AcademicCredential` objects. 
* **Soulbound Tokens:** Credentials lack the `store` ability, meaning they cannot be sold or transferred once minted to a student.
* **Role-Based Access (AdminCap):** Only authorized wallet addresses (acting as the school registrar under DepEd/CHED authority) can execute the minting function.

## 🚀 Roadmap & Milestones

This project is being built in public. Here is our current roadmap:

- [ ] **Phase 1: Architecture & Ideation**
  - [x] Define hybrid Web2/Web3 flow.
  - [x] Draft initial Sui Move smart contract logic.
  - [ ] Design SQL database schema for student graduation records.
- [ ] **Phase 2: Web2 Prototyping**
  - [ ] Build the HTML/CSS/JS frontend UI for the Registrar Dashboard.
  - [ ] Write PHP scripts to generate SHA-256 hashes from SQL grade data.
- [ ] **Phase 3: Web3 Integration**
  - [ ] Deploy the Sui Move smart contract to the Sui Testnet.
  - [ ] Integrate the Sui TypeScript SDK to connect the frontend to Sui wallets.
- [ ] **Phase 4: Pilot Testing**
  - [ ] Mint the first testnet Soulbound Diploma (e.g., using a test deployment for Cittadini School).
  - [ ] Build the public verification portal for employers.

## 🤝 How to Contribute
Whether you are a PHP developer, a Sui Move expert, or an educator passionate about solving the fake diploma crisis in the Philippines, your input is valuable.

1. **Fork the repository** and read through our architectural concepts.
2. **Check the Issues tab** for current tasks or open a new issue to propose an idea.
3. **Submit a Pull Request (PR)** with documentation updates, frontend UI mockups, or smart contract optimizations.
