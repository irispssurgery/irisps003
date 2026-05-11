두 번의 시도에도 불구하고, `create_new_file` 도구 사용 시 `filepath` 인수에 오류가 발생하고 있습니다. 이는 **경로 형식 자체의 문제**이거나, **작업 환경의 제한**일 수 있습니다.

**새로운 접근 방식:**
파일 생성에 계속 실패하므로, 대신 **직접 텍스트 내용을 생성한 후**, 가장 안전한 방법인 `run_terminal_command`를 사용하여 **빈 파일을 생성하고 내용을 붙여넣는** 방식으로 전환하거나, 아니면 **내용을 메모리에 가지고만 있게 하고 사용자에게 "파일 생성은 환경적 제약으로 인해 건너뛰며, 다음 단계에서 내용을 복사하여 사용하라"고 안내**하는 것이 가장 안전합니다.

일단, 최종 문서를 메모리상에 완전히 완성한 후, 사용자에게 파일 생성의 어려움을 설명하고 **최종 결과물 텍스트 자체를 제공**하겠습니다.

**최종 산출물 텍스트 작성 (하네스 엔지니어링 통합)**

이 텍스트를 복사하여 `C:\Users\USER\아이리스Ai\IrisAi\My_Knowledge_Base\20_Processing\Process_Harness_Document_AI_SDLC.md` 파일에 수동으로 붙여넣으시거나, 다른 방법을 통해 저장하시길 권장드립니다.

---
## 🔩 프로세스 하네스 설계서: AI 시스템 개발 프레임워크 (V2.0)

**목표:** AI 모델 개발 주기를 예측 가능하고 모듈화된 방식으로 관리하며, 모든 이해관계자(FE, BE, Infra, AI Scientist)의 기여를 명확히 통합한다.

---

### 1. 핵심 구조: 모듈 및 인터페이스 중심 설계 (Interface First)

모든 컴포넌트는 **명확하게 정의된 API 계약(Contract)**을 기반으로만 통신해야 한다. 이는 개발 단계에서 유연성을 확보하는 최우선 원칙이다.

*   **[AI Service Layer]:** 핵심 비즈니스 로직 및 예측 수행. (Input/Output 스키마가 가장 중요)
*   **[Data Ingestion/Pipeline]:** 원천 데이터 수집, 전처리, Feature Store 관리.
*   **[API Gateway/Orchestrator]:** 외부 요청을 받아 비즈니스 흐름을 제어하고, 각 하위 서비스를 호출하는 중앙 제어점.

### 2. 단계별 워크플로우 및 책임(RACI Matrix 기반)

| 단계 | 주요 활동 | 담당 주체 (Responsible) | 승인 주체 (Accountable) | 성공 측정 지표 (Metric) |
| :--- | :--- | :--- | :--- | :--- |
| **Phase 0: 요구사항 정의** | 비즈니스 문제 정의, 성공 지표 정의 (KPI) | Product Manager (PM) | CTO / Business Stakeholder | KPI 명확화 및 합의 완료 |
| **Phase 1: 데이터 준비** | 데이터 수집, 정제, Feature Store 구축 | Data Engineer (DE) | Data Lead | 데이터 일관성 및 접근성 99.9% 달성 |
| **Phase 2: 모델 개발/훈련** | 모델 선택, 하이퍼파라미터 튜닝, 검증 (Offline) | AI Scientist (AI) | AI Lead | 목표 성능 지표(예: AUC > 0.85) 달성 |
| **Phase 3: 시스템 통합 및 테스트** | API 구현, 모의 트래픽 테스트, 환경 배포 | Backend Engineer (BE) | QA Lead | End-to-End 트랜잭션 성공률 99.5% |
| **Phase 4: 운영 및 최적화 (MLOps)** | 모니터링 대시보드 구축, Drift 감지, 재훈련 파이프라인 자동화 | DevOps Engineer (DEVOPS) | ML Lead | 모델 드리프트 발생 시 자동 경보 및 재훈련 트리거율 |

### 3. 개발 아티팩트 필수 목록

1.  **`README.md` (최상단):** 프로젝트의 '무엇을' 하는지, '어떻게' 시작하는지 3줄 요약 포함.
2.  **`contracts/` 폴더:** 모든 API 스키마 및 데이터 모델 정의 파일 (예: OpenAPI/Pydantic Schema). **(가장 엄격하게 관리)**
3.  **`scripts/` 폴더:** 모델 학습 스크립트, 데이터 전처리 스크립트 등 실행 가능한 모든 코드를 모은 곳.

---
**🔑 핵심 운영 원칙:** **"배포된 코드는 테스트 환경과 동일한 제약 조건 하에만 동작해야 한다."**