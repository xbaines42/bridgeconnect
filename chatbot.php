<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once "db.php";

// ============================================================
// API KEY — loaded from .env (never commit .env to Git!)
// ============================================================
$env = parse_ini_file(__DIR__ . "/.env");
$api_key = $env["ANTHROPIC_API_KEY"] ?? "";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$input       = json_decode(file_get_contents('php://input'), true);
$user_message = isset($input['message']) ? trim($input['message']) : '';
$userContext  = isset($input['userContext']) ? $input['userContext'] : [];

if (empty($user_message)) {
    echo json_encode(['error' => 'No message provided']);
    exit();
}

// ============================================================
// Pull resources live from the database
// ============================================================
$resources_text = "";
$res = $conn->query("SELECT * FROM resources ORDER BY type, name");

if ($res && $res->num_rows > 0) {
    $grouped = [];
    while ($row = $res->fetch_assoc()) {
        $grouped[$row['type']][] = $row;
    }
    foreach ($grouped as $type => $items) {
        $resources_text .= strtoupper($type) . ":\n";
        foreach ($items as $item) {
            $resources_text .= "- " . $item['name'];
            if (!empty($item['address']))    $resources_text .= " | " . $item['address'];
            if (!empty($item['phone']))      $resources_text .= " | Phone: " . $item['phone'];
            if ($item['type'] === 'shelter') $resources_text .= " | Available Beds: " . $item['available_beds'];
            if (!empty($item['description']))$resources_text .= " | " . $item['description'];
            $resources_text .= "\n";
        }
        $resources_text .= "\n";
    }
} else {
    $resources_text = "No resources are currently listed.";
}

// ============================================================
// Build personalized user context string
// ============================================================
$user_name   = !empty($userContext['name'])   ? $userContext['name']   : 'the user';
$user_gender = !empty($userContext['gender']) ? $userContext['gender'] : 'prefer_not_to_say';
$user_about  = !empty($userContext['about'])  ? $userContext['about']  : '';

$gender_note = match($user_gender) {
    'female'     => "The user identifies as female. Prioritize women-friendly or women-only resources if available.",
    'male'       => "The user identifies as male.",
    'non_binary' => "The user identifies as non-binary. Use inclusive language.",
    default      => "Gender not specified. Use inclusive language."
};

$about_note = !empty($user_about)
    ? "The user shared this about their situation: \"$user_about\". Use this to personalize your recommendations."
    : "The user has not shared details about their situation.";

// ============================================================
// Build system prompt
// ============================================================
$system_prompt = "You are a warm, compassionate assistant for BridgeConnect, a platform that helps homeless and vulnerable people find local resources in Baltimore, MD.

USER PROFILE:
- Name: $user_name
- $gender_note
- $about_note

YOUR JOB:
Help this specific user find the most relevant resources based on their profile and situation. Be personal, warm, and concise. Address them by first name when natural.

AVAILABLE RESOURCES (live from database):
$resources_text

GUIDELINES:
- Tailor recommendations to the user's gender and situation whenever possible.
- For shelter questions, mention that bed counts are updated in real time on the app.
- If someone seems to be in crisis, respond with warmth and urgency and direct them to call 211.
- Keep responses short and scannable — bullet points are great.
- Only answer questions related to BridgeConnect resources and homeless/housing assistance.
- Do not answer questions unrelated to housing, food, medical, hygiene, or job support.";

// ============================================================
// Call Anthropic API
// ============================================================
$data = [
    'model'      => 'claude-sonnet-4-5',
    'max_tokens' => 1000,
    'system'     => $system_prompt,
    'messages'   => [
        ['role' => 'user', 'content' => $user_message]
    ]
];

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-api-key: ' . $api_key,
    'anthropic-version: 2023-06-01'
]);

$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['error' => 'API request failed. Check your API key.']);
    exit();
}

$result = json_decode($response, true);
$reply  = $result['content'][0]['text'] ?? 'Sorry, I could not get a response. Please try again.';

echo json_encode(['reply' => $reply]);
?>