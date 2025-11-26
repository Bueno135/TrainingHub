<?php
// ============================================
// src/Services/MatchingService.php
// ============================================
class MatchingService {
    private $db;
    private $freelanceRepository;
    private $professorRepository;

    public function __construct($db, $freelanceRepository, $professorRepository) {
        $this->db = $db;
        $this->freelanceRepository = $freelanceRepository;
        $this->professorRepository = $professorRepository;
    }

    /**
     * Encontra professores compatíveis com um freelance
     */
    public function findMatchingProfessores($freelanceId) {
        $freelance = $this->freelanceRepository->findById($freelanceId);
        
        if (!$freelance) {
            return [];
        }

        $filters = [];
        
        // Filtrar por cidade se o freelance tiver localização
        if (!empty($freelance['academia_cidade'])) {
            $filters['cidade'] = $freelance['academia_cidade'];
        }

        if (!empty($freelance['academia_estado'])) {
            $filters['estado'] = $freelance['academia_estado'];
        }

        // Filtrar por valor máximo se especificado
        if (!empty($freelance['valor_hora'])) {
            $filters['valor_max'] = $freelance['valor_hora'];
        }

        $professores = $this->professorRepository->search($filters);

        // Ordenar por compatibilidade (nota média + número de avaliações)
        usort($professores, function($a, $b) {
            $scoreA = ($a['nota_media'] ?? 5.0) * 10 + ($a['total_avaliacoes'] ?? 0);
            $scoreB = ($b['nota_media'] ?? 5.0) * 10 + ($b['total_avaliacoes'] ?? 0);
            return $scoreB <=> $scoreA;
        });

        return $professores;
    }

    /**
     * Encontra freelances compatíveis com um professor
     */
    public function findMatchingFreelances($professorId) {
        $professor = $this->professorRepository->findById($professorId);
        
        if (!$professor) {
            return [];
        }

        $filters = [];
        
        // Filtrar por localização
        if (!empty($professor['cidade'])) {
            $filters['cidade'] = $professor['cidade'];
        }

        if (!empty($professor['estado'])) {
            $filters['estado'] = $professor['estado'];
        }

        // Filtrar por valor mínimo do professor
        if (!empty($professor['valor_hora'])) {
            // Buscar freelances com valor maior ou igual ao do professor
            $filters['valor_min'] = $professor['valor_hora'];
        }

        $freelances = $this->freelanceRepository->findAll($filters);

        return $freelances;
    }

    /**
     * Calcula score de compatibilidade entre professor e freelance
     */
    public function calculateCompatibilityScore($professor, $freelance) {
        $score = 0;
        $maxScore = 100;

        // Localização (40 pontos)
        if (!empty($professor['cidade']) && !empty($freelance['academia_cidade'])) {
            if ($professor['cidade'] === $freelance['academia_cidade']) {
                $score += 40;
            } elseif (!empty($professor['estado']) && $professor['estado'] === $freelance['academia_estado']) {
                $score += 20;
            }
        }

        // Valor (30 pontos)
        if (!empty($professor['valor_hora']) && !empty($freelance['valor_hora'])) {
            $diff = abs($professor['valor_hora'] - $freelance['valor_hora']);
            $percentDiff = ($diff / $professor['valor_hora']) * 100;
            if ($percentDiff <= 10) {
                $score += 30;
            } elseif ($percentDiff <= 20) {
                $score += 20;
            } elseif ($percentDiff <= 30) {
                $score += 10;
            }
        }

        // Avaliação do professor (30 pontos)
        $notaMedia = $professor['nota_media'] ?? 5.0;
        $score += ($notaMedia / 5.0) * 30;

        return min($score, $maxScore);
    }
}

