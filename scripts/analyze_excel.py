#!/usr/bin/env python3
"""
Analisi avanzata di file Excel usando Pandas
Riceve il percorso del file come primo argomento
Output formattato su stdout per l'integrazione con Laravel

Features:
- Statistiche descrittive complete
- Correlazioni tra variabili numeriche
- Rilevamento outlier (metodo IQR)
- Analisi duplicati
- Distribuzione valori categorici
- Suggerimenti automatici
"""

import sys
import io
import pandas as pd
import numpy as np
from pathlib import Path
import warnings

# Sopprimi TUTTI i warning per evitare interferenze con l'output
warnings.filterwarnings('ignore')

# Fix encoding per Windows/MAMP
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')
    sys.stderr.reconfigure(encoding='utf-8')

def analyze_correlations(df):
    """Analizza correlazioni tra colonne numeriche"""
    numeric_cols = df.select_dtypes(include=[np.number]).columns
    
    if len(numeric_cols) < 2:
        return "Non ci sono abbastanza colonne numeriche per calcolare correlazioni."
    
    corr_matrix = df[numeric_cols].corr()
    
    output = "Matrice di correlazione:\n"
    output += corr_matrix.to_string()
    output += "\n\n"
    
    # Trova coppie con alta correlazione
    high_corr = []
    for i in range(len(corr_matrix.columns)):
        for j in range(i+1, len(corr_matrix.columns)):
            corr_value = corr_matrix.iloc[i, j]
            if abs(corr_value) > 0.7:
                high_corr.append((
                    corr_matrix.columns[i],
                    corr_matrix.columns[j],
                    corr_value
                ))
    
    if high_corr:
        output += "Coppie con alta correlazione (|r| > 0.7):\n"
        for col1, col2, corr in high_corr:
            output += f"  - {col1} <-> {col2}: {corr:.3f}\n"
    else:
        output += "Nessuna coppia con correlazione forte (|r| > 0.7).\n"
    
    return output

def analyze_outliers(df):
    """Rileva outlier usando il metodo IQR"""
    numeric_cols = df.select_dtypes(include=[np.number]).columns
    
    if len(numeric_cols) == 0:
        return "Non ci sono colonne numeriche per rilevare outlier."
    
    output = "Outlier rilevati (metodo IQR - Interquartile Range):\n\n"
    outliers_found = False
    
    for col in numeric_cols:
        Q1 = df[col].quantile(0.25)
        Q3 = df[col].quantile(0.75)
        IQR = Q3 - Q1
        
        lower_bound = Q1 - 1.5 * IQR
        upper_bound = Q3 + 1.5 * IQR
        
        outliers = df[(df[col] < lower_bound) | (df[col] > upper_bound)]
        
        if len(outliers) > 0:
            outliers_found = True
            percentage = (len(outliers) / len(df)) * 100
            output += f"Colonna '{col}':\n"
            output += f"  - Numero outlier: {len(outliers)} ({percentage:.2f}%)\n"
            output += f"  - Range normale: [{lower_bound:.2f}, {upper_bound:.2f}]\n"
            output += f"  - Valori outlier: {outliers[col].tolist()[:10]}"  # Max 10
            if len(outliers) > 10:
                output += f" ... (e altri {len(outliers)-10})"
            output += "\n\n"
    
    if not outliers_found:
        output += "Nessun outlier significativo rilevato.\n"
    
    return output

def analyze_duplicates(df):
    """Analizza righe duplicate"""
    duplicates = df.duplicated()
    num_duplicates = duplicates.sum()
    
    if num_duplicates == 0:
        return "Nessuna riga duplicata trovata."
    
    percentage = (num_duplicates / len(df)) * 100
    output = f"Righe duplicate: {num_duplicates} ({percentage:.2f}%)\n\n"
    
    # Mostra alcuni esempi di duplicati
    duplicate_rows = df[duplicates].head(5)
    if len(duplicate_rows) > 0:
        output += "Esempi di righe duplicate:\n"
        output += duplicate_rows.to_string(index=True)
    
    return output

def analyze_categorical(df):
    """Analizza distribuzione valori per colonne categoriche"""
    categorical_cols = df.select_dtypes(include=['object', 'category']).columns
    
    if len(categorical_cols) == 0:
        return "Non ci sono colonne categoriche/testuali da analizzare."
    
    output = "Distribuzione valori per colonne categoriche:\n\n"
    
    for col in categorical_cols:
        output += f"Colonna '{col}':\n"
        output += f"  - Valori unici: {df[col].nunique()}\n"
        
        # Top 10 valori più frequenti
        value_counts = df[col].value_counts().head(10)
        output += "  - Top 10 valori più frequenti:\n"
        for val, count in value_counts.items():
            percentage = (count / len(df)) * 100
            output += f"    • {val}: {count} ({percentage:.2f}%)\n"
        
        output += "\n"
    
    return output

def generate_suggestions(df):
    """Genera suggerimenti automatici basati sui dati"""
    suggestions = []
    
    # Check valori mancanti
    missing_pct = (df.isnull().sum() / len(df)) * 100
    high_missing = missing_pct[missing_pct > 20]
    if len(high_missing) > 0:
        suggestions.append(
            f"⚠️  Alcune colonne hanno molti valori mancanti (>20%): "
            f"{', '.join(high_missing.index.tolist())}. "
            f"Considera di rimuoverle o imputare i valori."
        )
    
    # Check duplicati
    num_duplicates = df.duplicated().sum()
    if num_duplicates > 0:
        pct = (num_duplicates / len(df)) * 100
        if pct > 5:
            suggestions.append(
                f"⚠️  Il dataset contiene {num_duplicates} righe duplicate ({pct:.1f}%). "
                f"Verifica se sono duplicati reali o dati legittimi."
            )
    
    # Check colonne con un solo valore
    single_value_cols = [col for col in df.columns if df[col].nunique() == 1]
    if single_value_cols:
        suggestions.append(
            f"ℹ️  Colonne con un solo valore (costanti): {', '.join(map(str, single_value_cols))}. "
            f"Potrebbero essere rimosse per semplificare l'analisi."
        )
    
    # Check dataset size
    if len(df) < 30:
        suggestions.append(
            "⚠️  Il dataset è molto piccolo (<30 righe). "
            "Le analisi statistiche potrebbero non essere affidabili."
        )
    
    # Check colonne numeriche con bassa varianza
    numeric_cols = df.select_dtypes(include=[np.number]).columns
    for col in numeric_cols:
        if df[col].std() / (df[col].mean() + 1e-10) < 0.01:  # Coefficient of variation molto basso
            suggestions.append(
                f"ℹ️  La colonna '{col}' ha varianza molto bassa. "
                f"Potrebbe essere quasi costante."
            )
    
    if not suggestions:
        suggestions.append("✓  Il dataset sembra ben strutturato, nessun problema evidente rilevato.")
    
    return suggestions

def analyze_excel_file(file_path):
    """
    Analizza un file Excel e stampa informazioni dettagliate
    
    Args:
        file_path: Percorso al file Excel da analizzare
    """
    try:
        # Verifica che il file esista
        if not Path(file_path).exists():
            print(f"ERRORE: Il file '{file_path}' non esiste.", file=sys.stderr)
            sys.exit(1)
        
        # Legge TUTTI i fogli del file Excel e li concatena in un unico DataFrame
        # supporta sia .xlsx che .xls
        all_sheets = pd.read_excel(file_path, sheet_name=None, engine='openpyxl')
        
        # Nome del file
        file_name = Path(file_path).name
        
        # Numero di fogli trovati
        num_sheets = len(all_sheets)
        
        # Concatena tutti i fogli in un unico DataFrame
        if num_sheets == 1:
            # Un solo foglio: usa quello direttamente
            df = list(all_sheets.values())[0]
            sheet_info = f"1 foglio: {list(all_sheets.keys())[0]}"
        else:
            # Più fogli: concatena tutti
            df = pd.concat(all_sheets.values(), ignore_index=True)
            sheet_names = ', '.join(all_sheets.keys())
            sheet_info = f"{num_sheets} fogli concatenati: {sheet_names}"
        
        # Converti i nomi delle colonne in stringhe per evitare errori con colonne numeriche
        df.columns = df.columns.astype(str)
        
        print("=" * 80)
        print(f"ANALISI FILE EXCEL: {file_name}")
        print("=" * 80)
        print()
        
        # 1. Informazioni generali sul DataFrame
        print("--- INFORMAZIONI GENERALI ---")
        print(f"File: {file_name}")
        print(f"Fogli: {sheet_info}")
        print(f"Numero di righe: {len(df)}")
        print(f"Numero di colonne: {len(df.columns)}")
        print(f"Colonne presenti: {', '.join(map(str, df.columns.tolist()))}")
        print()
        
        # 2. Dettagli sui tipi di dati (df.info())
        print("--- DETTAGLI TIPI DI DATI (df.info()) ---")
        buffer = io.StringIO()
        df.info(buf=buffer)
        info_output = buffer.getvalue()
        print(info_output)
        print()
        
        # 3. Prime 10 righe del dataset
        print("--- PRIME 10 RIGHE (df.head(10)) ---")
        print(df.head(10).to_string(index=True))
        print()
        
        # 4. Statistiche descrittive
        print("--- STATISTICHE DESCRITTIVE (df.describe()) ---")
        # include='all' per includere anche colonne non numeriche
        print(df.describe(include='all').to_string())
        print()
        
        # 5. Valori mancanti
        print("--- VALORI MANCANTI ---")
        missing_counts = df.isnull().sum()
        if missing_counts.sum() > 0:
            print("Colonne con valori mancanti:")
            for col, count in missing_counts[missing_counts > 0].items():
                percentage = (count / len(df)) * 100
                print(f"  - {col}: {count} ({percentage:.2f}%)")
        else:
            print("Nessun valore mancante nel dataset.")
        print()
        
        # 6. Ultimi 5 record (tail)
        print("--- ULTIMI 5 RECORD (df.tail(5)) ---")
        print(df.tail(5).to_string(index=True))
        print()
        
        # 7. Analisi correlazioni
        print("--- CORRELAZIONI TRA VARIABILI NUMERICHE ---")
        print(analyze_correlations(df))
        print()
        
        # 8. Analisi outlier
        print("--- ANALISI OUTLIER ---")
        print(analyze_outliers(df))
        print()
        
        # 9. Analisi duplicati
        print("--- ANALISI DUPLICATI ---")
        print(analyze_duplicates(df))
        print()
        
        # 10. Analisi colonne categoriche
        print("--- DISTRIBUZIONE VALORI CATEGORICI ---")
        print(analyze_categorical(df))
        print()
        
        # 11. Suggerimenti automatici
        try:
            print("--- SUGGERIMENTI AUTOMATICI ---")
            suggestions = generate_suggestions(df)
            for i, suggestion in enumerate(suggestions, 1):
                print(f"{i}. {suggestion}")
            print()
        except Exception as e:
            print(f"(Impossibile generare suggerimenti: {str(e)})")
            print()
        
        print("=" * 80)
        print("ANALISI COMPLETATA CON SUCCESSO")
        print("=" * 80)
        
        # Forza il flush dell'output per assicurarsi che tutto sia scritto
        sys.stdout.flush()
        sys.stderr.flush()
        
    except FileNotFoundError:
        print(f"ERRORE: File non trovato: {file_path}", file=sys.stderr)
        sys.stderr.flush()
        sys.exit(1)
    except pd.errors.EmptyDataError:
        print(f"ERRORE: Il file '{file_path}' è vuoto o non contiene dati validi.", file=sys.stderr)
        sys.stderr.flush()
        sys.exit(1)
    except Exception as e:
        print(f"ERRORE durante l'analisi del file: {str(e)}", file=sys.stderr)
        import traceback
        traceback.print_exc(file=sys.stderr)
        sys.stderr.flush()
        sys.exit(1)

def main():
    """Funzione principale"""
    if len(sys.argv) < 2:
        print("Uso: python analyze_excel.py <percorso_file_excel>", file=sys.stderr)
        print("Esempio: python analyze_excel.py data/vendite.xlsx", file=sys.stderr)
        sys.exit(1)
    
    file_path = sys.argv[1]
    analyze_excel_file(file_path)

if __name__ == "__main__":
    main()


