-- Create the database
CREATE DATABASE IF NOT EXISTS sui_ed_credentials;
USE sui_ed_credentials;

-- 1. Students Table: Holds basic demographic and DepEd LRN data
CREATE TABLE students (
    student_id VARCHAR(20) PRIMARY KEY, -- School's internal ID (e.g., 2026-0001)
    lrn VARCHAR(12) UNIQUE NOT NULL,    -- DepEd Learner Reference Number (12 digits)
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Programs/Strands Table: e.g., JHS, SHS - TVL ICT, SHS - STEM
CREATE TABLE programs (
    program_id INT AUTO_INCREMENT PRIMARY KEY,
    program_code VARCHAR(20) NOT NULL,
    program_name VARCHAR(100) NOT NULL
);

-- 3. Subjects Table: Master list of all subjects taught
CREATE TABLE subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) NOT NULL, -- e.g., "PROG101", "CS-12"
    subject_name VARCHAR(100) NOT NULL,
    units DECIMAL(3,1) DEFAULT 1.0
);

-- 4. Grades Table: The granular data that will eventually be hashed
CREATE TABLE grades (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    subject_id INT NOT NULL,
    school_year VARCHAR(9) NOT NULL, -- e.g., "2025-2026"
    semester TINYINT,                -- 1 or 2 (for SHS)
    final_grade DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id)
);

-- 5. Blockchain Credentials Table: The bridge between Web2 and Web3
CREATE TABLE blockchain_credentials (
    credential_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    program_id INT NOT NULL,
    document_hash VARCHAR(64) UNIQUE NOT NULL,   -- The SHA-256 hash of all their grades
    sui_wallet_address VARCHAR(66) NOT NULL,     -- The student's Web3 wallet address
    sui_transaction_digest VARCHAR(100) UNIQUE,  -- The receipt ID from the Sui network once minted
    mint_status ENUM('pending', 'minted', 'failed') DEFAULT 'pending',
    minted_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (program_id) REFERENCES programs(program_id)
);

