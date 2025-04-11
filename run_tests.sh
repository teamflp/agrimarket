#!/bin/bash

# Nom du script: run_tests.sh
# Usage: ./run_tests.sh [options]

# Vérifier si PHPUnit est installé via Composer
if [ ! -f "./vendor/bin/phpunit" ]; then
    echo "Erreur: PHPUnit n'est pas installé. Veuillez l'installer via Composer avant de continuer."
    exit 1
fi

# Options par défaut
TEST_DIR="tests"              # Répertoire des tests
CONFIG_FILE="phpunit.xml"     # Fichier de configuration PHPUnit
COVERAGE_REPORT="coverage"    # Répertoire pour le rapport de couverture
OUTPUT_FILE=""               # Fichier pour stocker les résultats
OUTPUT_FORMAT="text"          # Format de la sortie (text, xml, json)
WATCH_DIRS="tests src"         # Répertoires à surveiller en mode watch
WATCH_EVENTS="modify,create,delete" # Événements à surveiller en mode watch
DOCKER_MODE=false            # Exécuter les tests dans un conteneur Docker

# Fonction pour afficher l'aide
function show_help {
    echo "Usage: $0 [options]"
    echo ""
    echo "Options:"
    echo "  -h, --help            Afficher ce message d'aide et quitter"
    echo "  -d, --dir DIR         Spécifier le répertoire des tests (par défaut: tests)"
    echo "  -c, --config FILE     Spécifier le fichier de configuration PHPUnit (par défaut: phpunit.xml)"
    echo "  -t, --test FILE       Exécuter un fichier de test spécifique"
    echo "  -f, --filter FILTER   Filtrer les tests à exécuter avec une chaîne de filtre"
    echo "  --coverage            Générer un rapport de couverture de code"
    echo "  --watch               Ré-exécuter automatiquement les tests lors de modifications (nécessite inotify-tools)"
    echo "  --output FILE         Stocker les résultats dans un fichier"
    echo "  --format FORMAT       Format de la sortie (text, xml, json) (par défaut: text)"
    echo "  --watch-dirs DIRS     Répertoires à surveiller en mode watch (par défaut: tests src)"
    echo "  --watch-events EVENTS Événements à surveiller en mode watch (par défaut: modify,create,delete)"
    echo "  --docker              Exécuter les tests dans un conteneur Docker"
}

# Traitement des arguments de ligne de commande
WATCH_MODE=false
COVERAGE=false
TEST_FILE=""
FILTER=""

while [[ "$#" -gt 0 ]]; do
    case $1 in
        -d|--dir) TEST_DIR="$2"; shift ;;
        -c|--config) CONFIG_FILE="$2"; shift ;;
        -t|--test) TEST_FILE="$2"; shift ;;
        -f|--filter) FILTER="$2"; shift ;;
        --coverage) COVERAGE=true ;;
        --watch) WATCH_MODE=true ;;
        --output) OUTPUT_FILE="$2"; shift ;;
        --format) OUTPUT_FORMAT="$2"; shift ;;
        --watch-dirs) WATCH_DIRS="$2"; shift ;;
        --watch-events) WATCH_EVENTS="$2"; shift ;;
        --docker) DOCKER_MODE=true ;;
        -h|--help) show_help; exit 0 ;;
        *) echo "Erreur: Paramètre inconnu: $1"; show_help; exit 1 ;;
    esac
    shift
done

# Validation des options
if [[ -n "$OUTPUT_FORMAT" && ! "$OUTPUT_FORMAT" =~ ^(text|xml|json)$ ]]; then
    echo "Erreur: Format de sortie invalide. Utilisez text, xml ou json."
    exit 1
fi

# Construction de la commande PHPUnit
CMD="./vendor/bin/phpunit"

if [[ -f "$CONFIG_FILE" ]]; then
    CMD+=" -c $CONFIG_FILE"
fi

if [[ "$COVERAGE" = true ]]; then
    CMD+=" --coverage-html $COVERAGE_REPORT"
fi

if [[ -n "$TEST_FILE" ]]; then
    CMD+=" $TEST_FILE"
else
    CMD+=" $TEST_DIR"
fi

if [[ -n "$FILTER" ]]; then
    CMD+=" --filter $FILTER"
fi

if [[ -n "$OUTPUT_FILE" ]]; then
    CMD+=" --log-$OUTPUT_FORMAT $OUTPUT_FILE"
fi

# Fonction pour exécuter les tests
function run_tests {
    clear
    echo "Exécution des tests..."
    if [[ "$DOCKER_MODE" = true ]]; then
        # shellcheck disable=SC2046
        docker run --rm -v $(pwd):/var/www/html votre_image_nom $CMD
    else
        $CMD
    fi

    if [[ $? -eq 0 ]]; then
        echo "Tous les tests ont réussi."
    else
        echo "Certains tests ont échoué."
    fi
}

if [[ "$WATCH_MODE" = true ]]; then
    # Vérifier si inotifywait est installé
    if ! command -v inotifywait &> /dev/null
    then
        echo "Erreur: inotify-tools n'est pas installé. Veuillez l'installer pour utiliser le mode watch."
        exit 1
    fi

    echo "Mode watch activé. Surveillance des modifications dans les répertoires $WATCH_DIRS..."
    while true; do
        run_tests
        inotifywait -e "$WATCH_EVENTS" -r "$WATCH_DIRS"
    done
else
    run_tests
fi