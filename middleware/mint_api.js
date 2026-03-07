// middleware/mint_api.js

import { getFullnodeUrl, SuiClient } from '@mysten/sui/client';
import { Transaction } from '@mysten/sui/transactions';
import { Ed25519Keypair } from '@mysten/sui/keypairs/ed25519';
import express from 'express';

const app = express();
app.use(express.json());

// 1. Initialize Sui Client (Connected to Testnet for safety)
const client = new SuiClient({ url: getFullnodeUrl('testnet') });

// 2. Setup the Admin Keypair (The school/DepEd's authorized wallet)
// In a production environment, NEVER hardcode this. Use environment variables (.env).
// This private key must belong to the account that holds the `AdminCap`.
const ADMIN_SECRET_KEY = process.env.ADMIN_SECRET_KEY || 'your_secret_key_here';
const keypair = Ed25519Keypair.deriveKeypair(ADMIN_SECRET_KEY);

// Your deployed Smart Contract details
const PACKAGE_ID = '0x123456789abcdef...'; // Replace with actual deployed package ID
const ADMIN_CAP_ID = '0xabcdef1234567...'; // Replace with the AdminCap object ID

// 3. The API Endpoint that PHP will call
app.post('/api/mint', async (req, res) => {
    try {
        // Data received from your PHP script
        const { studentName, studentId, course, documentHash, issuer, recipientWallet } = req.body;

        console.log(`Starting mint process for ${studentName} from ${issuer}...`);

        // 4. Create a Programmable Transaction Block (PTB)
        const tx = new Transaction();

        // Call the smart contract function
        tx.moveCall({
            target: `${PACKAGE_ID}::diploma::mint_credential`,
            arguments: [
                tx.object(ADMIN_CAP_ID),             // The authorization proof
                tx.pure.string(studentName),         // e.g., "Juan Dela Cruz"
                tx.pure.string(studentId),           // e.g., "2026-0001"
                tx.pure.string(course),              // e.g., "SHS - TVL ICT"
                tx.pure.string(documentHash),        // The SHA-256 hash from PHP
                tx.pure.string(issuer),              // e.g., "A certain Science High School"
                tx.pure.address(recipientWallet)     // The student's Sui wallet address
            ]
        });

        // 5. Sign and Execute the Transaction on the Sui Network
        const result = await client.signAndExecuteTransaction({
            signer: keypair,
            transaction: tx,
            options: {
                showEffects: true,
                showObjectChanges: true,
            },
        });

        console.log("Minting Successful! Digest:", result.digest);

        // 6. Return the receipt (digest) back to PHP to save in the SQL database
        res.status(200).json({
            success: true,
            message: "Credential minted successfully on Sui.",
            transaction_digest: result.digest 
        });

    } catch (error) {
        console.error("Minting failed:", error);
        res.status(500).json({ success: false, error: error.message });
    }
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Sui Middleware API listening at http://localhost:${PORT}`);
});
