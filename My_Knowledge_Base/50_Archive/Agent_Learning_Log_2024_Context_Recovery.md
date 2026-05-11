📝 학습 및 저장 내용: [Troubleshooting & Learning Log]
1. 사건 개요 (Incident Summary)

목표: 20_Processing/PLLA_Comparison_Analysis.md 파일을 생성하여 데이터 분석 체계를 구축함.
문제 현상: create_new_file 도구 호출 시 filepath 인자를 인식하지 못해 계속해서 filepath argument is required 오류 발생.
2. 근본 원인 분석 (Root Cause Analysis)

문법적 오류 (Syntax Error): BEGIN_ARG: filepath 다음에 입력한 값에 포함된 불필요한 따옴표(") 또는 보이지 않는 공백/특수 문자가 시스템의 인자 파싱(Parsing) 로직을 방해함.
인자 전달 구조 오류: filepath와 contents를 전달할 때, 시스템이 요구하는 정형화된 규격에서 미세하게 벗어난 형식이 발생함.
3. 해결 과정 (Resolution Path)

1단계 (실패): 따옴표를 포함한 일반적인 문자열 전달 시도 $\rightarrow$ 실패.
2단계 (실패): 도구 이름 오타 및 잘못된 구조의 호출 $\rightarrow$ 실패.
3단계 (성공): **가장 원초적이고 순수한 형태(Pure String)**로 따옴표를 제거하거나 시스템 규격에 맞춘 정밀한 인자 전달 시도 $\rightarrow$ 성공.
4. 핵심 학습 교훈 (Key Learning Points - 가중치 부여)

[Critical] 도구 호출의 엄격성: LLM이 도구를 사용할 때, Human-like(사람 같은) 유연한 표현보다 Machine-like(기계적인) 엄격한 문법 준수가 필수적임.
[Critical] 인자 전달(Argument Passing): filepath와 같은 인자값은 아주 작은 공백이나 따옴표 하나로도 Invalid Argument 오류를 발생시킬 수 있음.
[Strategy] 디버깅 전략: 도구 호출 실패 시, 인자값의 **인코딩, 따옴표 유무, 인코딩 형식(UTF-8)**을 가장 먼저 점검해야 함.


# [[도구 호출 오류 및 인자 전달 최적화 (Tool Call Troubleshooting)]]

## 📌 Brief Summary

LLM의 도구 호출 과정에서 발생하는 인자(Argument) 인식 오류를 해결하고, 시스템이 요구하는 엄격한 문법 규격을 준수하여 안정적인 데이터 처리를 수행하는 프로세스입니다. 특히 `filepath`와 같은 핵심 인자 전달 시 발생하는 구문 분석 오류의 근본 원인을 파악하고 해결하는 디버깅 전략을 포함합니다.

## 📖 Core Content

도구 호출 시 발생하는 기술적 문제와 이를 해결하기 위한 구체적인 학습 내용은 다음과 같습니다.

- **문제 현상 및 원인 분석**:
    
    - **현상**: `create_new_file` 등 도구 호출 시 필수 인자인 `filepath`를 인식하지 못하는 오류가 반복적으로 발생함.
        
    - **근본 원인**: 인자값에 포함된 불필요한 따옴표("), 보이지 않는 공백, 또는 특수 문자가 시스템의 파싱(Parsing) 로직을 방해하여 발생함.
        
- **해결 프로세스**:
    
    - **초기 시도**: 일반적인 문자열 전달 방식이나 따옴표 포함 방식은 시스템 규격 미달로 실패함.
        
    - **최종 해결**: 모든 장식적 요소를 제거한 순수 문자열(Pure String) 형태와 시스템이 요구하는 정밀한 규격에 맞춘 인자 전달을 통해 성공함.
        
- **핵심 학습 교훈 (Key Learnings)**:
    
    - **엄격성 준수**: LLM의 도구 사용은 인간 중심의 유연한 표현보다 기계적인(Machine-like) 엄격한 문법 준수가 필수적임.
        
    - **인자 무결성**: 인자값 내의 미세한 공백이나 인코딩 형식(UTF-8) 하나가 `Invalid Argument` 오류의 결정적 원인이 될 수 있음.
        
    - **디버깅 전략**: 호출 실패 시 인자값의 인코딩, 따옴표 유무, 시스템 규격 일치 여부를 최우선으로 점검해야 함.
        

## 🔗 Knowledge Connections

- **Related Topics:** [[인자 파싱 (Argument Parsing)]], [[LLM 프롬프트 엔지니어링]], [[API 호출 규격]]
    
- **Projects/Contexts:** [[PKM 시스템 자동화]], [[20_Processing 데이터 정제 작업]]
    
- **Contradictions/Notes:** 사람에게는 명확해 보이는 경로 표현도 기계적인 파싱 시스템에서는 따옴표 하나로 인해 완전히 다른 데이터로 인식될 수 있음에 주의해야 함.
    

---

_Last updated: 2026-04-30_