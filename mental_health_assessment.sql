-- Create the mental health assessments table
CREATE TABLE mental_health_assessments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    sleep_rest_score INT NOT NULL,
    body_energy_score INT NOT NULL,
    emotions_mood_score INT NOT NULL,
    social_support_score INT NOT NULL,
    mind_focus_score INT NOT NULL,
    self_care_score INT NOT NULL,
    red_flags_score INT NOT NULL,
    total_score INT NOT NULL,
    feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_user_date (user_id, date)
);

-- Create the assessment answers table
CREATE TABLE assessment_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assessment_id INT NOT NULL,
    section_name VARCHAR(50) NOT NULL,
    question_number INT NOT NULL,
    answer BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES mental_health_assessments(id)
);