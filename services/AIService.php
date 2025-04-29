<?php
/**
 * AIService - Handles integration with OpenAI for document generation
 */
class AIService {
    private $conn;
    private $apiKey;
    private $model;
    private $apiUrl = 'https://api.openai.com/v1/chat/completions';
    
    public function __construct($conn = null) {
        $this->conn = $conn;
        $this->apiKey = OPENAI_API_KEY;
        $this->model = OPENAI_MODEL;
    }
    
    /**
     * Generate document content using AI
     * 
     * @param string $templateContent Template content with placeholders
     * @param array $variables Variables to replace in the template
     * @return string Generated document content
     */
    public function generateDocumentContent($templateContent, $variables) {
        // First, replace variables in the template
        $documentContent = $this->replaceVariables($templateContent, $variables);
        
        // If no API key is available, just return the content with replaced variables
        if (empty($this->apiKey)) {
            return $documentContent;
        }
        
        try {
            // Prepare the prompt for AI enhancement
            $prompt = $this->prepareAIPrompt($documentContent, $variables);
            
            // Call OpenAI API
            $enhancedContent = $this->callOpenAI($prompt);
            
            // If AI enhancement was successful, return it
            if (!empty($enhancedContent)) {
                return $enhancedContent;
            }
        } catch (Exception $e) {
            // Log the error
            error_log("AI enhancement failed: " . $e->getMessage());
        }
        
        // If anything fails, return the basic content with replaced variables
        return $documentContent;
    }
    
    /**
     * Replace variables in template content
     * 
     * @param string $templateContent Template content with placeholders
     * @param array $variables Variables to replace in the template
     * @return string Content with replaced variables
     */
    private function replaceVariables($templateContent, $variables) {
        $content = $templateContent;
        
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', htmlspecialchars($value, ENT_QUOTES), $content);
        }
        
        return $content;
    }
    
    /**
     * Prepare prompt for AI enhancement
     * 
     * @param string $content Content with replaced variables
     * @param array $variables Variables used in the document
     * @return string Prompt for OpenAI
     */
    private function prepareAIPrompt($content, $variables) {
        // Create a system message explaining the task
        $systemMessage = "Você é um assistente especializado na elaboração de documentos oficiais para a Câmara Municipal de Arapongas, seguindo todas as normas da legislação brasileira, especialmente a Lei de Licitações 14.133. Seu trabalho é revisar, aprimorar e completar o documento mantendo o formato e estrutura, mas melhorando a linguagem, adicionando detalhes técnicos relevantes e garantindo que o documento seja tecnicamente preciso e legalmente adequado.";
        
        // Additional context based on variables
        $context = "Este é um documento do tipo: ";
        if (isset($variables['tipo_documento'])) {
            $context .= $variables['tipo_documento'];
        } else {
            $context .= "documento oficial";
        }
        
        // Add information about the document purpose if available
        if (isset($variables['finalidade'])) {
            $context .= ". Finalidade: " . $variables['finalidade'];
        }
        
        // Create the user message with the content to enhance
        $userMessage = $context . "\n\nPor favor, revise e aprimore o seguinte documento, mantendo seu formato mas melhorando a linguagem, completude e adequação legal:\n\n" . $content;
        
        // Return combined messages
        return [
            [
                'role' => 'system',
                'content' => $systemMessage
            ],
            [
                'role' => 'user',
                'content' => $userMessage
            ]
        ];
    }
    
    /**
     * Call OpenAI API
     * 
     * @param array $messages Array of messages for the API
     * @return string Generated content or empty string on failure
     */
    private function callOpenAI($messages) {
        // Set up cURL request
        $ch = curl_init($this->apiUrl);
        
        $postData = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 4000
        ];
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Process response
        if ($httpCode == 200) {
            $responseData = json_decode($response, true);
            
            if (isset($responseData['choices'][0]['message']['content'])) {
                return $responseData['choices'][0]['message']['content'];
            }
        }
        
        // Log error if any
        if ($httpCode != 200) {
            error_log("OpenAI API call failed with HTTP code $httpCode: $response");
        }
        
        return '';
    }
    
    /**
     * Generate document based on template and variables
     * 
     * @param string $templateContent Template content with placeholders
     * @param array $variables Variables to replace in the template
     * @return string Generated document content
     */
    public function generateDocument($templateContent, $variables) {
        return $this->generateDocumentContent($templateContent, $variables);
    }

    /**
     * Analyze and suggest improvements for a document
     * 
     * @param string $documentContent Document content to analyze
     * @return array Suggestions for improvement
     */
    public function analyzeDocument($documentContent) {
        // If no API key is available, return empty suggestions
        if (empty($this->apiKey)) {
            return [];
        }
        
        try {
            // Create system message
            $systemMessage = "Você é um especialista em documentos oficiais e legislação brasileira, especialmente a Lei de Licitações 14.133. Analise o documento e forneça 3 a 5 sugestões específicas de melhoria para torná-lo mais claro, completo e aderente às normas legais. Forneça sua resposta em formato de lista, com cada sugestão em um item separado.";
            
            // Create user message
            $userMessage = "Por favor, analise o seguinte documento e forneça sugestões de melhoria:\n\n" . $documentContent;
            
            // Call OpenAI API
            $response = $this->callOpenAI([
                [
                    'role' => 'system',
                    'content' => $systemMessage
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ]);
            
            // Process response to extract suggestions
            if (!empty($response)) {
                // Split response by new lines and filter out empty lines
                $lines = array_filter(explode("\n", $response), function($line) {
                    return trim($line) !== '';
                });
                
                // Extract suggestions based on list format (numbered or bullet points)
                $suggestions = [];
                foreach ($lines as $line) {
                    if (preg_match('/^(\d+\.|\*|\-)\s+(.+)$/', $line, $matches)) {
                        $suggestions[] = trim($matches[2]);
                    } else {
                        // If it's not in list format, include the whole line
                        $suggestions[] = trim($line);
                    }
                }
                
                return $suggestions;
            }
        } catch (Exception $e) {
            error_log("Document analysis failed: " . $e->getMessage());
        }
        
        return [];
    }
}
?>
