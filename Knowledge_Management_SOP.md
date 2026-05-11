# 🧠 지식 관리 시스템(PKM) 구축 및 프로세스 마스터 기록

**최종 업데이트:** 2024-05-22
**상태:** ✅ 프로세스 검증 완료 (Verified)
**핵한 키워드:** #GTD #InboxZero #Knowledge_Pipeline #Automation

---

## 1. 시스템 아키텍처 (Folder Structure)
본 시스템은 데이터의 성숙도(Maturity)에 따라 5단계 폴더 구조를 가집니다.

| 폴더명 | 역할 | 성격 |
| :--- | :--- | :--- |
| `10_Inbox` | 원시 데이터 수집 (Raw Data) | 휘발성, 무작ance, 빠름 |
| `20_Processing` | 데이터 정제 및 분류 (Refining) | 작업 중, 분석, 판단 |
| `30_Permanent` | 정제된 지식 저장 (Golden Assets) | 영구적, 구조화, 신뢰 가능 |
| `40_Projects` | 실행 및 결과물 도출 (Execution) | 목표 지향, 마감 있음, 액션 |
| `50_Archive` | 종료된 프로젝트 보관 (History) | 완료됨, 기록용 |

---

## 2. 핵심 워크플플로우 (The Workflow)

### 📥 Step 1: Capture (수집)
- **방법:** 생각, 링크, 메모 등을 아무 형식이나 상관없이 `10_Inbox`에 즉시 투입.
- **규칙:** 형식을 고민하지 마라. 오직 '기록'에만 집중한다.

### ⚙️ Step 2: Clarify & Organize (정제 및 정리)
- **방법:** `10_Inbox`의 파일을 `20_Processing`으로 이동.
- **판단 기준:**
    1. **삭제(Trash):** 가치가 없는 정보인가? $\to$ 삭제.
    2. **실행(Action):** 프로젝트나 할 일인가? $\to$ `40_Projects`로 이동.
    3. **지식(Knowledge):** 나중에 다시 볼 가치가 있는 정보인가? $\to$ `30_Permanent`로 이동.
- **정제 작업:** 데이터에 `Processed: ` 태그를 붙이거나, 마크다운(Markdown) 형식으로 구조화함.

### 🏛️ Step 3: Store (저장)
- **방법:** 정제된 결과물을 `30_Permanent`에 구조화된 형태로 저장.
- **목표:** 언제든 검색 가능하고(Searchable), 연결 가능한(Linkable) 상태로 유지.

---

## 3. 자동화 엔진 (Automation Engine)
본 시스템은 PowerShell 스크립트를 활용하여 수동 작업의 피로도를 줄임.

- **자동화 기능 1:** `gemma4_guide.md`를 각 폴더에 일괄 생성하여 SOP(표준운영절차)를 전파.
- **자동화 기능 2:** `Inbox` $\to$ `Processing` 단계로 데이터를 이동시키고 태그를 붙이는 파이프라인 가동.

---

## 4. 학습된 교훈 (Lessons Learned)
1. **Input의 단순화:** 수집 단계에서 형식을 따지지 않아야 '기록의 누락'이 발생하지 않는다.
2. **Processing의 분리:** 생각(수집)과 판단(정리)을 분리해야 뇌의 과부하를 막을 수 있다.
3. **Automated Traceability:** 스크립트를 통해 작업 이력을 남기면 시스템의 신뢰도가 높아진다.

---
**Engineer Note:** 이 문서는 시스템의 뼈대이며, 새로운 도구(AI, Notion, Obsidian 등)가 도입되어도 변하지 않는 불변의 로직을 담고 있음.

# [[PKM 시스템 아키텍처 및 워크플로우 (Master Process)]]

## 📌 Brief Summary

데이터의 성숙도에 따른 5단계 폴더 구조와 자동화 엔진을 결합하여 정보의 수집부터 실행까지 관리하는 개인 지식 관리 시스템(PKM)입니다. 생각(수집)과 판단(정리)을 분리함으로써 뇌의 부하를 줄이고, 자동화 스크립트를 통해 시스템의 신뢰성과 지속 가능성을 확보하는 것이 핵심입니다.

## 📖 Core Content

본 시스템은 데이터의 생애주기를 관리하기 위해 다음과 같은 구조화된 프로세스를 따릅니다.

### 1. 시스템 아키텍처 (Maturity-based Structure)

데이터의 성격과 처리 단계에 따라 5가지 폴더로 구분하여 관리합니다.

- **10_Inbox (Raw Data)**: 형식에 구애받지 않고 무작위로 발생하는 모든 원시 데이터를 빠르게 수집하는 휘발성 공간입니다.
    
- **20_Processing (Refining)**: 수집된 데이터를 분석하고 가치를 판단하여 정제하는 작업 공간입니다.
    
- **30_Permanent (Golden Assets)**: 정제가 완료되어 구조화된, 신뢰할 수 있는 영구적 지식 저장소입니다.
    
- **40_Projects (Execution)**: 마감이 존재하며 구체적인 목표와 액션을 포함하는 실행 중심의 공간입니다.
    
- **50_Archive (History)**: 완료된 프로젝트나 종료된 작업물들을 기록용으로 보관하는 공간입니다.
    

### 2. 핵심 워크플로우 (The 3-Step Process)

- **Capture (수집)**: 형식을 고민하지 않고 오직 '기록'에만 집중하여 10_Inbox에 투입합니다.
    
- **Clarify & Organize (정제 및 정리)**: 20_Processing 단계에서 정보를 분석하여 가치가 없으면 삭제하고, 실행 과제는 40_Projects로, 지식 자산은 30_Permanent로 이동시킵니다. 이 과정에서 마크다운 구조화 및 태그 작업이 수행됩니다.
    
- **Store (저장)**: 정제된 결과물을 언제든 검색 및 연결이 가능한 상태로 30_Permanent에 유지합니다.
    

### 3. 자동화 및 운영 원칙

- **자동화 엔진**: PowerShell 스크립트를 활용해 각 폴더에 SOP(표준운영절차) 파일을 일괄 생성하거나, 데이터 이동 및 태그 부착 파이프라인을 가동하여 수동 작업의 피로도를 낮춥니다.
    
- **뇌 부하 방지**: 수집 단계에서의 'Input 단순화'와 정리 단계에서의 'Processing 분리'를 통해 인지적 과부하를 방지하고 기록의 누락을 막습니다.
    

## 🔗 Knowledge Connections

- **Related Topics:** [[GTD (Getting Things Done)]], [[Inbox Zero]], [[PowerShell Automation]], [[Standard Operating Procedure (SOP)]]
    
- **Projects/Contexts:** [[Knowledge_Pipeline]], [[Gemma4_Guide_Project]]
    
- **Contradictions/Notes:** 본 로직은 특정 도구(AI, Notion, Obsidian 등)의 변경에 영향을 받지 않는 불변의 지식 관리 로직으로 설계되었습니다.
    

---

_Last updated: 2026-04-30_